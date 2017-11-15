//GLOBALS
var w3c = (document.getElementById) ? 1 : 0
var ns4 = (document.layers) ? 1 : 0  //browser detect for NS4 & W3C standards
var hasCookies = false;

// tests whether the user accepts cookies, and sets a flag.
if (document.cookie == '') {
    document.cookie = 'hasCookies=yes';
    if (document.cookie.indexOf('hasCookies=yes') != -1) hasCookies = true;
}
else hasCookies = true;

// returns an object reference.
function getObject(obj) {
    if (w3c)
        var theObj = document.getElementById(obj);
    else if (ns4)
        var theObj = eval("document." + obj);
    return theObj;
}

// swaps text in a layer.
function swapText(text, divID, innerDivID) {
    var content = "<span class=\"commandDesc\">" + text + "</span>";
    if (w3c) {
        var theObj = getObject(divID);
        if (theObj) theObj.innerHTML = text;
    }
    else if (ns4) {
        var innerObj = divID + ".document." + innerDivID;
        var theObj = getObject(innerObj);
        if (theObj) {
            theObj.document.open();
            theObj.document.write(content);
            theObj.document.close();
        }
    }
}

// sets a cookie in the browser.
function setCookie(name, value, hours, path) {
    if (hasCookies) {
        if (hours) {
            if ((typeof(hours) == 'string') && Date.parse(hours)) var numHours = hours;
            else if (typeof(hours) == 'number') var numHours = (new Date((new Date()).getTime() + hours * 3600000)).toGMTString();
        }
        document.cookie = name + '=' + escape(value) + ((numHours) ? (';expires=' + numHours) : '') + ((path) ? ';path=' + path : '');
    }
}

// reads a cookie from the browser
function readCookie(name) {
    if (document.cookie == '') return '';
    else {
        var firstChar, lastChar;
        var theBigCookie = document.cookie;
        firstChar = theBigCookie.indexOf(name);
        if (firstChar != -1) {
            firstChar += name.length + 1;
            lastChar = theBigCookie.indexOf(';', firstChar);
            if (lastChar == -1) lastChar = theBigCookie.length;
            return unescape(theBigCookie.substring(firstChar, lastChar));
        }
        else return '';
    }
}

/*  Netscape 4 resize fix */
function WM_netscapeCssFix() {
    if (document.WM.WM_netscapeCssFix.initWindowWidth != window.innerWidth || document.WM.WM_netscapeCssFix.initWindowHeight != window.innerHeight) {
        document.location = document.location;
    }
}

function WM_netscapeCssFixCheckIn() {
    if ((navigator.appName == 'Netscape') && (parseInt(navigator.appVersion) == 4)) {
        if (typeof document.WM == 'undefined') {
            document.WM = new Object;
        }
        if (typeof document.WM.WM_scaleFont == 'undefined') {
            document.WM.WM_netscapeCssFix = new Object;
            document.WM.WM_netscapeCssFix.initWindowWidth = window.innerWidth;
            document.WM.WM_netscapeCssFix.initWindowHeight = window.innerHeight;
        }
        window.onresize = WM_netscapeCssFix;
    }
}

WM_netscapeCssFixCheckIn();

function showHideModuleMouseOver(divID) {
    var theCookie = readCookie(divID);
    if ((theCookie == "e") || (theCookie == "")) {
        window.status = "Collapse";
    }
    else {
        window.status = "Expand";
    }
}

function showHideModule(divID, theme) {
    var state = toggleFoldyPersistState(divID);
    var ok = false;
    if (w3c) {
        var divIDobj = MM_findObj(divID);
        var tlobj = MM_findObj(divID + "tl");
        var toggleobj = MM_findObj(divID + "Toggle");
        if (divIDobj != null && tlobj != null && toggleobj != null) {

            ok = true;
            if (state == "c") {
                tlobj.src = "../themes/" + theme + "/images/spacer.gif";
                toggleobj.src = "../themes/" + theme + "/images/module_toggle_closed.gif";
                divIDobj.style.display = "none";
            } else {
                tlobj.src = "../themes/" + theme + "/images/spacer.gif";
                toggleobj.src = "../themes/" + theme + "/images/module_toggle_open.gif";
                divIDobj.style.display = "";
            }
        }
    }
    if (!ok) {
        document.location = document.location;
    }

    showHideModuleMouseOver(divID);
    //window.status = '';
}
function toggleFoldyPersistState(divID) {
    var theCookie = readCookie(divID);
    var state = "e";
    if ((theCookie == "e") || (theCookie == "")) {
        state = "c";
    }
    setCookie(divID, state, 'Wed 01 Jan 2020 00:00:00 GMT', '/');
    return state;
}

function MM_swapImgRestore() { //v3.0
    var i, x, a = document.MM_sr;
    for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) x.src = x.oSrc;
}

function MM_preloadImages() { //v3.0
    var d = document;
    if (d.images) {
        if (!d.MM_p) d.MM_p = new Array();
        var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
        for (i = 0; i < a.length; i++)
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
    }
}

function MM_findObj(n, d) { //v4.0
    var p, i, x;
    if (!d) d = document;
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all) x = d.all[n];
    for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
    for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
    if (!x && document.getElementById) x = document.getElementById(n);
    return x;
}

function MM_swapImage() { //v3.0
    var i, j = 0, x, a = MM_swapImage.arguments;
    document.MM_sr = new Array;
    for (i = 0; i < (a.length - 2); i += 3)
        if ((x = MM_findObj(a[i])) != null) {
            document.MM_sr[j++] = x;
            if (!x.oSrc) x.oSrc = x.src;
            x.src = a[i + 2];
        }
}

// Remove an array item at n
// 0-based

function MM_removeNthArrayItem(array, n) {
    var lhs = new Array();

    if (n > 0)
        lhs = array.slice(0, n);

    var rhs = new Array();

    if (n < array.length)
        rhs = array.slice(n + 1);

    var result = lhs.concat(rhs);

    return result;
}

// Does the array contain the given string?

function MM_arrayContainsString(array, item) {
    if (array == null)
        return false;

    var count = array.length;
    for (i = 0; i < count; i++) {
        if (array[i] == item)
            return true;
    }

    return false;
}

// remove the given string from the array of strings

function MM_removeStringFromArray(array, item) {
    if (array == null)
        return null;

    var count = array.length;
    for (i = 0; i < count; i++) {
        if (array[i] == item)
            return MM_removeNthArrayItem(array, i);
    }

    return array;
}

// a selectedItems array is kept in the form. It is an array of strings, each
// string being the name of a checkbox image. It doesn't actually have to be
// the name="foo" attribute of the HTML object itself, just any arbitrary name
// that is associated with this checkbox. The image name is the actual name of the image.

function MM_toggleItem(form, itemName, imageName, theme) {
    if (form.selectedItems == null)
        form.selectedItems = new Array();

    if (MM_arrayContainsString(form.selectedItems, itemName)) {
        form.selectedItems = MM_removeStringFromArray(form.selectedItems, itemName);
        document[imageName].src = '../themes/' + theme + '/images/checkbox_off_16.gif';
        //MM_swapImage(imageName, '', '../themes/'+theme+'/checkbox_off_16.gif', '1');
    } else {
        form.selectedItems[form.selectedItems.length] = itemName;
        document[imageName].src = '../themes/' + theme + '/images/checkbox_on_16.gif';
        //MM_swapImage(imageName, '', '../themes/'+theme+'/checkbox_on_16.gif', '1');
    }

    MM_updateButtons2(form, form.selectedItems);

}

function MM_selectAllItems(form, theme) {

    form.selectedItems = new Array();
    if (form.checkboxes) {
        var checkboxCount = form.checkboxes.length;
        for (i = 0; i < checkboxCount; i++) {
            var checkbox = form.checkboxes[i];
            if (-1 == document[checkbox.mImageName].src.indexOf('dim_16.gif')) {
                document[checkbox.mImageName].src = '../themes/' + theme + '/images/checkbox_on_16.gif';
                form.selectedItems[form.selectedItems.length] = checkbox.mName;
            }
        }
    }

    MM_updateButtons2(form, form.selectedItems);

}

function MM_deselectAllItems(form, theme) {
    form.selectedItems = new Array();
    if (form.checkboxes) {
        var checkboxCount = form.checkboxes.length;
        for (i = 0; i < checkboxCount; i++) {
            var checkbox = form.checkboxes[i];
            if (-1 == document[checkbox.mImageName].src.indexOf('dim_16.gif')) {
                document[checkbox.mImageName].src = '../themes/' + theme + '/images/checkbox_off_16.gif';
            }
        }
    }

    MM_updateButtons2(form, form.selectedItems);

}

// If all items are selected, deselect all. Otherwise select all.
function MM_toggleSelectedItems(form, theme) {
    if (!form.selectedItems)
        form.selectedItems = new Array();

    if (form.checkboxes) {
        if (form.selectedItems.length == form.checkboxes.length - MM_countDisabledCheckboxes(form))
            MM_deselectAllItems(form, theme);
        else
            MM_selectAllItems(form, theme);
    }
}

// counts the number of disabled checkboxes - used for deselect all
function MM_countDisabledCheckboxes(form) {
    var disabledCount = 0;
    if (form.checkboxes) {
        var checkboxCount = form.checkboxes.length;
        for (i = 0; i < checkboxCount; i++) {
            var checkbox = form.checkboxes[i];
            if (-1 != document[checkbox.mImageName].src.indexOf('dim_16.gif')) {
                disabledCount++;
            }
        }


    }


    return disabledCount;
}

// See SELECTIONPARAMNAME and SELECTIONPARAMDELIMITER in ListModuleTagBase.java.

function MM_doButtonAction(action, selectedItems) {

    // THESE MUST BE IN SYNC WITH ListModuleTagBase.java
    var SELECTIONPARAMNAME = 'id';
    var SELECTIONPARAMDELIMITER = '**';

    // If the action is a javascript action (starts with 'javascript')
    // then execute it immediately.

    if ((action.indexOf('javascript') == 0) || (action.indexOf('Javascript') == 0)) {
        eval(action);
    } else {

        var okay = true;
        // if action starts with "function:" call the function on the selection to see
        // if we can continue
        if ((action.indexOf('function:') == 0)) {
            okay = false;
            var idx = action.indexOf(":");
            if (idx + 1 < action.length) {
                action = action.substr(idx + 1);
                idx = action.indexOf(",");
                if (idx + 1 < action.length) {
                    var fxn = action.substr(0, idx);
                    action = action.substr(idx + 1);
                    fxn = eval(fxn);
                    if (typeof(fxn) == "function") {
                        okay = fxn(selectedItems);
                    }
                }
            }
        }

        if (okay) {
            var params = new String();

            if (selectedItems) {
                for (i = 0; i < selectedItems.length; i++) {
                    if (i > 0)
                        params = params.concat("**");

                    params = params.concat(selectedItems[i]);
                }
            }

            var url = action;

            if (params.length > 0) {
                if (url.indexOf('?') == -1)
                    url = url + '?';
                else
                    url = url + '&';

                url = url + 'id=' + params;
            }

            window.location = url;
        }

    }
}

function MM_updateButtons(form) {
    var dummy = new Array();
    MM_updateButtons2(form, dummy);
}

function MM_updateButtons2(form, selectedItems) {
    if (form.buttons) {
        var buttonCount = form.buttons.length;

        for (i = 0; i < buttonCount; i++) {
            var button = form.buttons[i];
            if (button) {
                button.update(selectedItems);
            }
        }
    }
}

function MM_getButtonWithName(form, buttonName) {

    if (form.buttons) {
        var buttonCount = form.buttons.length;

        for (i = 0; i < buttonCount; i++) {
            var button = form.buttons[i];
            if (button.mName == buttonName) {
                return button;
            }
        }
    }

    return null;
}

function MM_countFilesFolders(selectedItems) {
    var ret_obj = new Object();
    ret_obj["files"] = 0;
    ret_obj["folders"] = 0;
    var i = 0;
    for (i = 0; i < selectedItems.length; i++) {
        var ftype = null;
        if ((ftype = MM_findObj(selectedItems[i] + "ftype")) != null) {
            if (ftype.value == "file") ret_obj["files"]++;
            else if (ftype.value == "folder") ret_obj["folders"]++;
        }
    }
    return ret_obj;
}

function MM_oneFileOnly(selectedItems) {
    var ret = false;
    if (selectedItems.length == 1) {
        var ftype = null;
        if ((ftype = MM_findObj(selectedItems[0] + "ftype")) != null) {
            if (ftype.value == "file") ret = true;
        }
    }
    return ret;
}

function MM_atLeastOneFile(selectedItems) {
    var ret = false;
    if (selectedItems.length > 0) {
        var i = 0;
        while (!ret && i < selectedItems.length) {
            var ftype = null;
            if ((ftype = MM_findObj(selectedItems[i] + "ftype")) != null) {
                if (ftype.value == "file") ret = true;
            }
            ++i;
        }
    }
    return ret;
}

function MMCommandButton(name,
                         form,
                         action,
                         enabledImage,
                         overImage,
                         downImage,
                         disabledImage,
                         enableOnNoSelection,
                         enableOnSingleSelection,
                         enableOnMultipleSelection,
                         enabledCheckSelectionJS,
                         altText,
                         confirmation,
                         confirmationMessage) {
    this.mName = name;						// Name of the image
    this.mForm = form;						// The form object enclosing this button (to retrieve selections)
    this.mAction = action;					// Action to perform when clicking
    this.mEnabledImage = enabledImage;		// enabled image (String)
    this.mOverImage = overImage;			// over image (String)
    //this.mDownImage = downImage;			// down image (String)
    this.mDisabledImage = disabledImage;	// disabled image (String)
    this.mEnableOnNoSelection = enableOnNoSelection;
    this.mEnableOnSingleSelection = enableOnSingleSelection;
    this.mEnableOnMultipleSelection = enableOnMultipleSelection;
    this.mEnabledCheckSelectionJS = null;
    if (enabledCheckSelectionJS != '') {
        this.mEnabledCheckSelectionJS = eval(enabledCheckSelectionJS);
    }
    this.mAltText = altText;
    this.mConfirmation = confirmation;
    this.mConfirmationMessage = confirmationMessage;
    this.mEnabled = false;

    this.update = MMCommandButton_update;
    this.over = MMCommandButton_over;
    this.out = MMCommandButton_out;
    this.click = MMCommandButton_click;
}

function MMCommandButton_update(selectedItems) {
    if (this.mEnabledCheckSelectionJS != '' &&
        typeof(this.mEnabledCheckSelectionJS) == "function") {
        var isEnabled = this.mEnabledCheckSelectionJS(selectedItems);
        if (isEnabled === true) {
            document[this.mName].src = this.mEnabledImage;
            this.mEnabled = true;
        } else {
            document[this.mName].src = this.mDisabledImage;
            this.mEnabled = false;
        }
    }
    else {
        if (selectedItems.length == 0) {
            if (this.mEnableOnNoSelection === true) {
                document[this.mName].src = this.mEnabledImage;
                this.mEnabled = true;
            } else {
                document[this.mName].src = this.mDisabledImage;
                this.mEnabled = false;
            }
        }

        if (selectedItems.length == 1) {
            if (this.mEnableOnSingleSelection === true) {
                document[this.mName].src = this.mEnabledImage;
                this.mEnabled = true;
            } else {
                document[this.mName].src = this.mDisabledImage;
                this.mEnabled = false;
            }
        }

        if (selectedItems.length > 1) {
            if (this.mEnableOnMultipleSelection === true) {
                document[this.mName].src = this.mEnabledImage;
                this.mEnabled = true;
            } else {
                document[this.mName].src = this.mDisabledImage;
                this.mEnabled = false;
            }
        }
    }

}

function MMCommandButton_over() {
    if (this.mEnabled) {
        document[this.mName].src = this.mOverImage;
    }

    // To whom it may concern. If you are revisiting this code in order
    // to speed it up, note that the thing slowing down the rollovers is
    // this call to swapText.
    swapText(this.mAltText, this.mForm.tt, this.mForm.tt + "i");

    window.status = this.mAltText;
}

function MMCommandButton_out() {
    if (this.mEnabled) {
        document[this.mName].src = this.mEnabledImage;
    }
    swapText('', this.mForm.tt, this.mForm.tt + "i");

    window.status = '';
}

function MMCommandButton_click() {
    if (this.mEnabled) {
        //document[this.mName].src = this.mDownImage;

        if (this.mConfirmation) {
            if (!confirm(this.mConfirmationMessage)) {
                return;
            }
        }

        MM_doButtonAction(this.mAction, this.mForm.selectedItems);
    }
    swapText('', this.mForm.tt, this.mForm.tt + "i");
    window.status = '';
}

function MMCheckbox(name,
                    form,
                    imageName) {
    // The mName is the name of the checkbox that is passed on via POST
    this.mName = name;
    this.mForm = form;
    this.mImageName = imageName;
}

// A popup window for general use, but for invoking the content ui in particular
// For other purposes, a 500x350 window size is reasonable
function popUp(loc, w, h, menubar) {
    if (w == null) {
        w = 700;
    }
    if (h == null) {
        h = 500;
    }
    if (menubar == null || menubar == false) {
        menubar = "";
    } else {
        menubar = "menubar,";
    }

    //if( NS ) { w += 50; }
    // Need the var or else IE4 blows up not recognizing editorWin
    var editorWin = window.open(loc, 'editWin', menubar + 'resizable,scrollbars,width=' + w + ',height=' + h);
    //editorWin.focus(); //causing intermittent errors
}

// Used to submit a form if the user hits ENTER in the form - BAH
function submitOnEnter(form, e) {
    if (document.all) e = window.event;
    key = (document.layers) ? e.which : e.keyCode;
    if (13 == key) {
        if (form) form.submit();
        return false;
    }
    return true;
}

// Used to kill a key press event from bubbling up - BAH
function killKeyEvent(e) {
    if (document.all) e = window.event;
    key = (document.layers) ? e.which : e.keyCode;
    if (13 == key) e.cancelBubble = true;
}

// Used to limit the chars in a text or textarea input - BAH
// Must define variable maxChars & maxCharsError in the HTML tag or via javascript
function checkMaxChars(obj) {
    // current key is not counted in length yet
    if (obj.value.length >= obj.maxChars) {
        alert(obj.maxCharsError + ': ' + obj.maxChars);
        obj.value = obj.value.substr(0, obj.maxChars);
        return false;
    }
    return true;
}

function doSitespringHelper(url, msg, installurl) {
    var doIt = true;
    if (!gSitespringHelperOK && !confirm(msg)) {
        doIt = false;
    }
    if (doIt) window.location = url;
    return;
}
function doHelpWindow(helpURL) {
    mmHelpWindow = window.open(helpURL, "mmHelp");
    // Quarter second pause before focus to avoid JS errors
    setTimeout('mmHelpWindow.focus();', 250);
}
function focusAndSelect(fld) {
    var ualc = navigator.userAgent.toLowerCase();
    if (ualc.indexOf('compatible') > -1 || ualc.indexOf("macin") < 0 ||
        parseFloat(navigator.appVersion) >= 5.0) {
        var fldobj = MM_findObj(fld);
        if (fldobj != null) {
            fldobj.focus();
            fldobj.select();
        }
    }
}

//-->

