
/*******************************************************************************
*name: sortitems.js
*authour: Andrew Brown
*date: 28/02/2013
*Description:
*Makes an AJAX call to 'sortitems.php' and replaces the relevant section of the
*calling document with the sorted data. This function was based on a function
*taken from 'Learning PHP, MySQL, Javascript & CSS, 2nd edition'.
*Author: Robin Nixon.
*Publisher: O'Reilly.
*Date: August 2012.
*ISBN: 978-1-449-31926-7.  
*******************************************************************************/            
function sortItems(item, sorttype) {
    //identify item and type of sorting.
    params = "sort" + item + "=1&sorttype=" + sorttype 
    request = new ajaxRequest()
    //set headers.
    request.open("POST", "sortitems.php", true)
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    request.setRequestHeader("Content-length", params.length)
    request.setRequestHeader("Connection", "close")
                
    //if XMLHttpRequest object has been successful...            
    request.onreadystatechange = function() {
        if(this.readyState == 4) {
            if (this.status == 200) {
                if(this.responseText != null) {
                    //replace span tag in calling html page with sorted items.
                    document.getElementById(item + "s").innerHTML = this.responseText
                } else alert("Ajax error: No data recieved!")
            } else alert("Ajax error: " + this.statusText)
        }
    }
    request.send(params)
}

/*******************************************************************************
*This function was taken from 'Learning PHP, MySQL, Javascript & CSS, 2nd edition'.
*Author: Robin Nixon.
*Publisher: O'Reilly.
*Date: August 2012.
*ISBN: 978-1-449-31926-7.
*
*It creates a valid XMLHttpRequest object for the browser, and then returns 
*that object.
*******************************************************************************/
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
