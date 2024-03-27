<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audio Stream Recorder</title>
</head>
<body>

    <h1>Audio Stream Recorder</h1>

    <button id="recordButton" onclick="startRecording()">Record</button>
    <button id="stopRecordButton" onclick="stopRecording()" disabled>Stop Recording</button>

    <audio id="audioPlayer" controls></audio>

    <script>
        let mediaSource;
        let mediaRecorder;
        let recordedBlobs = [];
        let audioPlayer = document.getElementById('audioPlayer');

        function startRecording() {
            fetch('https://stream.revma.ihrhls.com/zc193')
                .then(response => response.body)
                .then(body => {
                    mediaSource = new MediaSource();
                    audioPlayer.src = URL.createObjectURL(mediaSource);
                    mediaSource.addEventListener('sourceopen', handleSourceOpen);
                    mediaSource.addEventListener('sourceended', handleSourceEnded);
                    mediaSource.addEventListener('sourceclose', handleSourceClosed);

                    body.pipeTo(new WritableStream({
                        write: chunk => {
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.ondataavailable({ data: chunk });
                            }
                        },
                        close: () => {
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                            }
                        }
                    }));
                })
                .catch(error => console.error('Error fetching audio stream:', error));
        }

        function handleSourceOpen() {
            mediaRecorder = new MediaRecorder(mediaSource);
            mediaRecorder.ondataavailable = handleDataAvailable;
            mediaRecorder.onstop = handleStop;
            document.getElementById('recordButton').disabled = true;
            document.getElementById('stopRecordButton').disabled = false;
            mediaRecorder.start();
        }

        function handleDataAvailable(event) {
            if (event.data.size > 0) {
                recordedBlobs.push(event.data);
            }
        }

        function handleStop() {
            const blob = new Blob(recordedBlobs, { type: 'audio/mp3' });
            recordedBlobs = [];
            audioPlayer.src = URL.createObjectURL(blob);
            document.getElementById('recordButton').disabled = false;
            document.getElementById('stopRecordButton').disabled = true;
        }

        function handleSourceEnded() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
        }

        function handleSourceClosed() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
        }

        function stopRecording() {
            mediaSource.endOfStream();
        }
    </script>

</body>
</html>