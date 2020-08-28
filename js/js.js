
function cancelPhoto() {
    let makePhotos = document.getElementsByClassName('makePhoto');
    for (let mp of makePhotos) {
        mp.setAttribute("hidden",true);
        }
    }

function makePhoto() {
    let canvas = document.getElementById('canvas');
    let context = canvas.getContext('2d');
    let video = document.getElementById('video');
    let photo = document.getElementById('photo');
    context.drawImage(video, 0, 0, 200, 200);
    let data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
    cancelPhoto();
    }

function editPhoto() {
    let makePhotos = document.getElementsByClassName('makePhoto');
    for (let mp of makePhotos) {
        mp.removeAttribute("hidden");
        }
    let video = document.getElementById('video');
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(function(stream) {
            video.srcObject = stream;
            video.play();}
            ).catch(function(err) {
                console.log("An error occurred: " + err);
                }
            );
        }
    }
