<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video Streaming</title>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('agoraVideo/index.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        /* Meeting Link Input */
        #linkUrl {
            width: 80%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        #linkUrl:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Buttons */
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 10px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Special Buttons */
        #leave-btn {
            background-color: #dc3545;
        }

        #leave-btn:hover {
            background-color: #b02a37;
        }

        #mic-btn,
        #camera-btn,
        #rec-btn {
            background-color: #28a745;
        }

        #mic-btn:hover,
        #camera-btn:hover,
        #rec-btn:hover {
            background-color: #1e7e34;
        }

        /* Stream Wrapper */
        #stream-wrapper {
            width: 90%;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        /* Video Streams */
        #video-streams {
            width: 100%;
            height: 400px;
            background-color: #000;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        /* Stream Controls */
        #stream-controls {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #stream-wrapper {
                width: 95%;
            }
        }
    </style>

</head>

<body>
    @if (!session()->has('meeting'))
        <input type="text" name="name" id="linkname" value="" placeholder="Enter your name"
            style="width: 80%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; outline: none; transition: 0.3s;">
    @endif

    <input type="text" id="linkUrl" value="{{ url('joinMeeting') }}/{{ $meeting->url }}"
        placeholder="Enter or generate a meeting link">

    <button id="join-form" style="display:none;"></button>
    <div id="">
    <button id="join-form2">Join Stream</button>
    <button id="join-forms" onclick="copyLink()">Copy Link</button>
    </div>

    <!-- Meeting Instance -->
    <div id="stream-wrapper">
        <div id="video-streams"></div>

        <div id="stream-controls">
            <button id="leave-btn">Leave Stream</button>
            <button id="mic-btn">Mic On</button>
            <button id="camera-btn">Camera On</button>

        </div>
    </div>

    <input id="appid" type="hidden" value="{{ $meeting->app_id }}" readonly>
    <input id="token" type="hidden" value="{{ $meeting->token }}" readonly>
    <input id="channel" type="hidden" value="{{ $meeting->channel }}" readonly>
    <input id="urlId" type="hidden" value="{{ $meeting->url }}" readonly>
    <input id="event" type="hidden" value="{{ $event }}" readonly>



    <input id="user_meeting" type="hidden" value="0">
    <input id="user_permission" type="hidden" value="0">

</body>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('agoraVideo/AgoraRTC_N-4.23.2.js') }}"></script>
<script src="{{ asset('agoraVideo/index.js') }}"></script> --}}

<script src="{{ asset('agoraVideo/AgoraRTC_N-4.23.2.js') }}"></script>
<script>
    // Agora client instance
    const client = AgoraRTC.createClient({
        mode: "rtc",
        codec: "vp8"
    });

    let localTracks = {
        videoTrack: null,
        audioTrack: null
    };

    let options = {
        appid: document.getElementById('appid').value.trim(),
        token: document.getElementById('token').value.trim(),
        channel: document.getElementById('channel').value.trim(),
    };

    // Join Stream
    document.getElementById('join-form2').addEventListener('click', async () => {
        try {
            console.log("Joining the stream...", options);
            // Join the Agora channel
            const response = await client.join(options.appid, options.channel, options.token);
            console.log("Join Response:", response);

            // Create and publish local tracks (audio and video)
            [localTracks.audioTrack, localTracks.videoTrack] = await Promise.all([

                AgoraRTC.createMicrophoneAudioTrack(),
                AgoraRTC.createCameraVideoTrack()
            ]);
            console.log('in mic');
            // Play the local video track
            localTracks.videoTrack.play('video-streams');

            // Publish the local tracks to the channel
            await client.publish(Object.values(localTracks));

            console.log("Successfully joined the stream!");
        } catch (error) {
            console.error("Failed to join the stream:", error);
        }
    });

    // Leave Stream
    document.getElementById('leave-btn').addEventListener('click', async () => {
        try {
            // Stop and close local tracks
            if (localTracks.audioTrack) {
                localTracks.audioTrack.stop();
                localTracks.audioTrack.close();
            }
            if (localTracks.videoTrack) {
                localTracks.videoTrack.stop();
                localTracks.videoTrack.close();
            }

            // Leave the Agora channel
            await client.leave();
            console.log("Successfully left the stream!");
        } catch (error) {
            console.error("Failed to leave the stream:", error);
        }
    });
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    //pusher web socket initialization
    var notificationChannel = $('#channel').val();
    var notificationEvent = $('#event').val();
    console.log('inside pushr notification code');
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('9a1cb6cd85f9d24d5a51', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe(notificationChannel);
    channel.bind(notificationEvent, function(data) {
        @if (session()->has('meeting'))
            //host user
            if (confirm(data.data.title)) {
                meetingApprove(data.data.$random_user, 2);
                $('#join-form').click();
            } else {
                //declined
                meetingApprove(data.data.$random_user, 3);
                alert('Host has been declined your entry');
            }
        @else

            //join user
            if (data.data.status == 2) {
                //meeting start

                $('#join-form').click();
                document.getElementById('stream-controls').style.display = 'flex';
            } else if (data.data.status == 3) {
                //entry declined

                alert('Host has been declined your entryy');
            } else {
                //entry denied

                alert('Host has been declined your entry');
            }
        @endif
        //alert(JSON.stringify(data));
        alert(data.data.title);
    });
</script>
<script>
    $('#join-form2').click(function() {
        //host user
        @if (session()->has('meeting'))
            $('#join-form').click();
            document.getElementById('stream-controls').style.display = 'flex';
        @else
            //join user
            var name = $('#linkname').val();
            if (name == '' || length.name < 1) {
                alert('Please enter your name');
                return;
            } else {
                saveUserName($name);
                alert('Request sent to host');
            }
        @endif
    })

    function saveUserName(name) {
        var url = "{{ url('saveUserName') }}";
        var random = "{{ session()->get('random_user') }}";
        var urlId = $('#urlId').val();
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {
                name: name,
                random: random,
                url: urlId
            },
            success: function(result) {

            }
        });
    }

    function meetingApprove($random_user, type) {
        var url = "{{ url('saveUserName') }}";
        var random = "{{ session()->get('random_user') }}";
        var urlId = $('#urlId').val();
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {
                name: name,
                random: random,
                url: urlId
            },
            success: function(result) {

            }
        });
    }

    function copyLink() {

        var urlPage = window.location.href;
        var temp = $("<input>");
        $("body").append(temp);
        temp.val(urlPage).select();
        document.execCommand("copy");
        temp.remove();
        alert('Link copied');
        $('#join-forms').text('Link copied');

    }
</script>

</html>
