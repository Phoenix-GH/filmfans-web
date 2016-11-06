(function ($) {
    $.fn.formatDate = function (format) {
        var months = { '1': '01', '2': '02', '3': '03', '4': '04', '5': '05', '6': '06', '7': '07', '8': '08', '9': '09', '10': '10',
            '11': '11', '12': '12'
        };
        var days = { '1': '01', '2': '02', '3': '03', '4': '04', '5': '05', '6': '06', '7': '07', '8': '08', '9': '09', '10': '10',
            '11': '11', '12': '12', '13': '13', '14': '14', '15': '15', '16': '16', '17': '17', '18': '18', '19': '19', '20': '20',
            '21': '21', '22': '22', '23': '23', '24': '24', '25': '25', '26': '26', '27': '27', '28': '28', '29': '29', '30': '30', '31': '31'
        };


        var element = this;
        element.attr("maxlength", "14");

        // if it is not numeric or frontslash, do not accept the input
        (element).keypress(function (e) {
            return IgnoreCharacter(e) || isNumeric(e);
        });


        (element).keyup(function (e) {

            // if it is still empty after keyup, just return
            if (IgnoreCharacter(e) || element.val() === "") {
                return false;
            }

            element.val(format === "dd/mm/yyyy" ? formatDDMMYYYY(element.val()) : (format === "yyyy/mm/dd" ? formatYYYYMMDD(element.val()) : formatMMDDYYYY(element.val())));


        });

        function IgnoreCharacter(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            //console.log(charCode);
            if (charCode == 8 //backspace
                || charCode == 37 || charCode == 39 // arrow keys 
                || charCode == 47 // slash 
                || charCode == 16 // shift
                || charCode == 46 // delete
                || charCode == 36 // home
                || charCode == 35 // delete
                ) {
                return true;
            }
            return false;
        }
        function isNumeric(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;

            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        function getDay(d) {
            return (days[d] != undefined)
                    ? days[d]
                    : d;
        }

        function getMonth(m) {
            return (months[m] != undefined)
                    ? months[m]
                    : m;

        }
        
        function formatMMDDYYYY(dob) {
            var formattedDate = dob;
            var day, month, year;

            if (dob.slice(dob.length - 1) === "/") {
                var dateParts = dob.split('/');
                var m1 = getDay(dateParts[0].trim());
                var d1 = getMonth(dateParts[1].trim());

                formattedDate = m1 + " / ";
                if (d1 != undefined && dateParts.length == 3) {
                    formattedDate += d1 + " / ";
                }
            } else if (dob.length < 2) {
                // nothing to do

            } else if (dob.length == 2) {
                // if it is less than equal to 31, append a slash
                if (dob < 13) {
                    formattedDate = dob + " / ";
                } else {
                    var m = getDay(dob.slice(0, 1));
                    var d = getMonth(dob.slice(1));

                    formattedDate = m + " / ";

                    if (d > 0) {
                        formattedDate += d + " / "
                    } else {
                        formattedDate += d;
                    }
                }
            } else if (dob.length == 5) {
                // day has been formated
                // nothing to do
            } else if (dob.length == 10) {
                // day and month have been formated
                // nothing to do
            } else if (dob.length > 5 && dob.length < 10) {
                var dateParts = dob.split('/');
                var m1 = dateParts[0]
                var d1 = dateParts[1];

                // if date has already been input
                formattedDate = m1.trim() + " / ";

                if (d1 > 10 || dob.length == 7) {
                    formattedDate += d1.trim() + " / ";
                } else {
                    formattedDate += d1.trim();
                }

            }

            return formattedDate;
        }

        function formatDDMMYYYY(dob) {
            var formattedDate = dob;
            var day, month, year;

            if (dob.slice(dob.length - 1) === "/") {
                var dateParts = dob.split('/');
                var d1 = getDay(dateParts[0].trim());
                var m1 = getMonth(dateParts[1].trim());

                formattedDate = d1 + " / ";
                if (m1 != undefined && dateParts.length == 3) {
                    formattedDate += m1 + " / ";
                }
            } else if (dob.length < 2) {
                // nothing to do

            } else if (dob.length == 2) {
                // if it is less than equal to 31, append a slash
                if (dob < 32) {
                    formattedDate = dob + " / ";
                } else {
                    var d = getDay(dob.slice(0, 1));
                    var m = getMonth(dob.slice(1));

                    formattedDate = d + " / ";

                    if (m > 0) {
                        formattedDate += m + " / "
                    } else {
                        formattedDate += m;
                    }
                }
            } else if (dob.length == 5) {
                // day has been formated
                // nothing to do
            } else if (dob.length == 10) {
                // day and month have been formated
                // nothing to do
            } else if (dob.length > 5 && dob.length < 10) {
                var dateParts = dob.split('/');
                var d1 = dateParts[0]
                var m1 = dateParts[1];

                // if date has already been input
                formattedDate = d1.trim() + " / ";

                if (m1 > 10 || dob.length == 7) {
                    formattedDate += m1.trim() + " / ";
                } else {
                    formattedDate += m1.trim();
                }

            }

            return formattedDate;
        }

        function formatYYYYMMDD(dob) {
            var formattedDate = dob;
            var day, month, year;
            var dateParts = dob.split('/');
            year = dateParts[0].trim();
            if (dateParts.length > 1)
                month = getMonth(dateParts[1].trim());
            if (dateParts.length > 2)
                day = dateParts[2].trim();

            if (dob.slice(dob.length - 1) === "/") {
                formattedDate = year + " / ";
                if (month != undefined && dateParts.length == 3) {
                    formattedDate += month + " / ";
                }
            } else if (dob.length < 2) {
                // nothing to do

            } else if (dob.length == 2) {
                if (dob > 20) {
                    formattedDate = '19' + dob + " / ";
                }
                if (dob < 19) {
                    formattedDate = '20' + dob + " / ";
                }
            } else if (dob.length == 4) {       //2014 / 10 / 11
                formattedDate = dob + " / ";    //012345678901234
            } else if (dob.length < 9) {
                // year have been formated
                // nothing to do
            } else if (dob.length == 9) {

                formattedDate = year + " / ";
                var part = dob.slice(7);

                // if it is less than equal to 13, append a slash
                if (part < 13) {
                    formattedDate += part + " / ";
                } else {
                    var m = getDay(part.slice(0, 1));
                    var d = getMonth(part.slice(1));

                    formattedDate += m + " / ";

                    if (d > 0)
                        formattedDate += d;
                }
            } else if (dob.length > 9 && dob.length < 12) {

                formattedDate = year + " / ";

                if (month > 10 || dob.length == 10) {
                    formattedDate += month + " / ";
                } else {
                    formattedDate += month;
                }
            } else if (dob.length > 12 && day > 0) {

                if (day > 3) {
                    day = getDay(day);
                }

                if (day > 31) {
                    day = day.slice(0, 1);
                }

                formattedDate = year + " / " + month + " / " + day;
            }

            return formattedDate;
        }

    };
})(jQuery);





