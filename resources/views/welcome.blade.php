<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>Document</title>
</head>

<body>
    @foreach ($blogs as $blog)
    <h1>{{$blog->title}}</h1>
    <iframe
        width="600px"
        height="300px"
        src="https://www.youtube.com/embed/{{$blog->youtube_url}}"
        title="YouTube video player"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
    ></iframe>
    @endforeach
</body>

</html>