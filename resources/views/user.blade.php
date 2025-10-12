<html lang="en">
@vite(['resources/css/app.css', 'resources/js/app.js'])

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class=" border-8 border-slate-400">
    <div class="text-slate-500 m-5 grid grid-flow-row gap-2 ">
        <div class="border-2 border-slate-300 text-4xl grid grid-flow-col align-middle text-center">
            Register
        </div>
        <div class="flex gap-3 align-middle items-center">
            <div>Name</div>
            <input type="text" placeholder="">
        </div>
        <div class="flex gap-3 align-middle items-center">
            <div>Email</div>
            <input type="text" placeholder="">
        </div>
        <div class="flex gap-3 align-middle items-center">
            <div>Password</div>
            <input type="text" placeholder="">
        </div>
    </div>
</body>

</html>