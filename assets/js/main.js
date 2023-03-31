'use strict';

//buttons
let callBtn = $('#callBtn');
let callBox = $('#callBox');
let answerBtn = $('#answerBtn');
let declineBtn = $('#declineBtn');
let alertBox = $('#alertBox');

let pc;
let sendTo = callBtn.data('user');
let localStream;

//video elements
const localVideo = document.querySelector("#localVideo");
const remoteVideo = document.querySelector("#remoteVideo");

//mediaInfo
const mediaConst = {
    video:true
};

//what to receive from other client
const options = {
    offerToReceiveVideo: 1,
}

function getConn() {
    if(!pc) {
        pc = new RTCPeerConnection();        
    }
}

//ask for media input
async function getCam() {
    let mediaStream;

    try {
        if(!pc) {
            await getConn();
        }
        mediaStream = await navigator.mediaDevices.getUserMedia(mediaConst);
        localVideo.srcObject = mediaStream;
        localStream = mediaStream;
        localStream.getTracks().forEach( track => pc.addTrack(track, localStream));
    } catch(error) {
        console.log(error);
    }
}

async function createOffer(sendTo) {
    await sendIceCandidate(sendTo);
    await pc.createOffer(options);
    await pc.setLocalDescription(pc.localDescription);
    send('client-offer', pc.localDescription, sendTo);
}

async function createAnswer(sendTo, data) {
    if(!pc) {
        await getConn();
    }

    if(!localStream) {
        await getCam();
    }

    await sendIceCandidate(sendTo);
    await pc.setRemoteDescription(data);
    await pc.createAnswer();
    await pc.setLocalDescription(pc.localDescription);
    send('client-answer', pc.localDescription, sendTo);
}

function sendIceCandidate(sendTo) {
    pc.onicecandidate = e => {
        if(e.candidate !== null) {
            //send ice candidate to other client
            send('client-candidate',e.candidate, sendTo);
        }
    }

    pc.ontrack = e => {
        remoteVideo.srcObject = e.streams[0];
    }
}

function hangup() {
    send('client-hangup', null, sendTo);
    pc.close();
    pc = null
}

$('#callBtn').on('click', () => {
    getCam();
    send('is-client-ready', null,sendTo);
});

$('#hangupBtn').on('click', () => {
    hangup();
    location.reload(true);
});

//websocket
conn.onopen = function(e) {
    console.log('Connected to websocket');
}

// conn.onmessage = async function(e) {
//     console.log('Siwooooo');
// }

conn.onmessage = async function(e) {
    console.log('hello');
    let message = JSON.parse(e.data);
    let by = message.by;
    let data = message.data;
    let type = message.type;
    let profileImage = message.profileImage;
    let username = message.username;
    $('#username').text(username);
    $('#profileImage').attr('src', profileImage); 

    switch(type) {
        case 'client-candidate':
            if(pc.localDescription) {
                await pc.addIceCandidate(new RTCIceCandidate(data));
            }
        break;

        case 'is-client-ready':
            if(!pc) {
                await getConn();
            }
            if(pc.iceConnectionState === "connected") {
                send('client-already-oncall');
            } else {
                //display popup
                displayCall();
                if(window.location.href.indexOf(username) > -1) {
                    answerBtn.on('click', () => {
                        console.log('answered'); // remark: work
                        send('client-is-ready', null, sendTo);
                    });
                } else {
                    answerBtn.on('click', () => {
                        console.log('answered'); // remark: work
                        redirectToCall(username, by);
                    });
                }
                declineBtn.on('click', () => {
                    console.log('rejected'); // remark: work
                    send('client-rejected', null, sendTo);
                    location.reload(true);
                });
            }
        break;

        case 'client-answer':
            if(pc.localDescription) {
                await pc.setRemoteDescription(data);
                $('#callTimer').timer({format: '%m:%s'});
            }
        break;

        case 'client-offer':
            createAnswer(sendTo, data);
            $('#callTimer').timer({format: '%m:%s'});
        break;

        case 'client-is-ready':
            // alert('client is ready'); // remark:work
            createOffer(sendTo);
        break;

        case 'client-already-oncall':
            displayAlert(username,profileImage,'is on another call'); 
            //display popup right here
            setTimeout('window.location.reload(true)', 2000);
        break;

        case 'client-rejected':
            displayAlert(username,profileImage,'is busy'); 
            setTimeout('window.location.reload(true)', 2000);
        break;

        case 'client-hangup':
            displayAlert(username,profileImage,'Disconnected the call'); 
            setTimeout('window.location.reload(true)', 2000);
        break;
    }
}

function send(type,data,sendTo) {
    conn.send(JSON.stringify({
        sendTo:sendTo,
        type:type,
        data:data
    }));
}

function displayCall() {
    callBox.removeClass('hidden');
}

function displayAlert(username,profileImage,message) {
    alertBox.find('#alertName').text(username);
    alertBox.find('#alertImage').attr('src',profileImage);
    alertBox.find('#alertMessage').text(message);    
}

function redirectToCall(username, sendTo) {
    if(window.location.href.indexOf(username) == -1) {
        sessionStorage.setItem('redirect', true);
        sessionStorage.setItem('sendTo', sendTo);
        window.location.href = '/webrtc/'+username;
    }
}

if(sessionStorage.getItem('redirect')) {
    sendTo = sessionStorage.getItem('sendTo');
    let waitForWs = setInterval(() => {
        if(conn.readyState === 1) {
            send('client-is-ready', null, sendTo);
            clearInterval(waitForWs);
        }
    }, 500);
    sessionStorage.removeItem('redirect');
    sessionStorage.removeItem('sendTo');
}