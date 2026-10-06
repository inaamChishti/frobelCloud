
<div class="container text-center">
    <h2>File Preview</h2>

    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
        <img src="{{ $file }}" alt="Image" class="img-fluid">
    @elseif(in_array($extension, ['mp4', 'webm']))
        <video controls width="600">
            <source src="{{ $file }}" type="video/{{ $extension }}">
            Your browser does not support the video tag.
        </video>
    @elseif($extension === 'pdf')
        <iframe src="{{ $file }}" width="100%" height="600px"></iframe>
        <script>
            setTimeout(function () {
                window.close();
            }, 1000); // Give user a second to read message
        </script>
    @else
        <p>Unsupported file format.</p>
    @endif
</div>

