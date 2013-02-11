<html>

    <head>
        <title>Ajaxtest</title>
        <script>
            function ajaxRequest() {
                try {
                    var request = new XMLHttpRequest()
                } catch (e1) {
                    try {
                        request = new ActiveXObject("Msxml2.XMLHTTP")
                    } catch (e2) {
                        try {
                            request = new ActiveXObject("Microsoft.XMLHTTP")
                        } catch (e3) {
                            request = false
                        }
                    }
                }
                return request
            }
            function getTitles() {
                params = "gettitles=1"
                request = new ajaxRequest()
                request.open("POST", "gettitles.php", true)
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
                request.setRequestHeader("Content-length", params.length)
                request.setRequestHeader("Connection", "close")
                
                request.onreadystatechange = function() {
                    if(this.readyState == 4) {
                        if (this.status == 200) {
                            if(this.responseText != null) {
                                document.getElementById("replace").innerHTML = this.responseText
                            } else alert("Ajax error: No data recieved!")
                        } else alert("Ajax error: " + this.statusText)
                    }
                }
                request.send(params)
            }

        </script>
    </head>    
    <body>
        <span id="replace">This text will be replaced!</span>
        <table>
            <tr>
                <td><form onclick="getTitles()"><input type="button" name="tracks" value="get tracks"/></form></td>
            </tr>
        </table>
    </body>

</html>