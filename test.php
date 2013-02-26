        <script>
            /*******************************************************************
             *This function was taken from 'Learning PHP, MySQL, Javascript
             *& CSS, 2nd edition'.
             *Author: Robin Nixon.
             *Publisher: O'Reilly.
             *Date: August 2012.
             *ISBN: 978-1-449-31926-7.
             *
             *It creates a valid XMLHttpRequest object for the browser, and then
             *returns that object.
             ******************************************************************/
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
            
            function getTitles(items, sorttype) {
                params = "get" + items + "=1&sorttype=" + sorttype 
                request = new ajaxRequest()
                request.open("POST", "gettitles.php", true)
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
                request.setRequestHeader("Content-length", params.length)
                request.setRequestHeader("Connection", "close")
                
                request.onreadystatechange = function() {
                    if(this.readyState == 4) {
                        if (this.status == 200) {
                            if(this.responseText != null) {
                                document.getElementById(items).innerHTML = this.responseText
                            } else alert("Ajax error: No data recieved!")
                        } else alert("Ajax error: " + this.statusText)
                    }
                }
                request.send(params)
            }

        </script>
