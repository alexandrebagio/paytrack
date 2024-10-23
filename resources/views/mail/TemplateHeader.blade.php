<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{env('APP_NAME')}}</title>
</head>
<style>
    body {
        font-family: "open sans", "helvetica neue", helvetica, arial, sans-serif;
        margin: 0 auto;
        width: 600px;
    }

    .header {
        padding: 1em;
        background-color: #5641ff;
        text-align: center;
        color: #ffffffcc;
        border-top-left-radius: 1em;
        border-top-right-radius: 1em;
    }

    .body {
        border-radius: 1em;
        margin: 0 auto;
        margin-top: -1.5em;
        width: 500px;
        background-color: #fff;
        padding: 1em;
    }

    .footer {
        margin-top: 4em;
        border-radius: 1em;
        padding: 1em;
        background-color: #7968ff;
        color: #ffffffcc;
        font-size: 11px;
        font-style: italic;
    }
</style>
<body>
    <div class="header">
        <h1>{{ env('APP_NAME') }} </h1>  
    </div>