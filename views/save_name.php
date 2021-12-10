<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/library.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" crossorigin="anonymous"></script>

</head>

<body>

    <form onsubmit="saveName(this); return false">
        <input id="name" name="name" type="text">
    </form>

    <script>
    async function saveName(form) {
        console.log(form)
        const name = formToJSON(form)
        console.log(name)
        await $.ajax({
            type: "POST",
            url: "save_name",
            data: JSON.stringify(name),
            success: function(response) {
                console.log(response)
                return
            },
            error: function(result) {
                console.log(response)

                return
            }
        });
    }
    </script>
</body>

</html>