
function save_current_voting() {
    jQuery('#x-submit-save-voting').click(function () {
        jQuery('form#x-sumbit-current-votings-form').submit(function (e) {
            e.preventDefault()
            console.warn("saving current votings")

            jQuery.post("options.php", jQuery('form#x-sumbit-current-votings-form').serialize(),
                //jQuery(this).ajaxSubmit({
                // success: 
                function () {
                    alert("values successively saved")
                    //jQuery('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
                    //jQuery('#saveMessage').append("<p><?php echo htmlentities(__('Settings Saved Successfully','wp'),ENT_QUOTES); ?></p>").show();
                },
                'json',
                //timeout: 5000
            );
            showXNotice("x-success-msg")
            return false;
        });
    })
}

function clickFormSubmitButton() {
    setTimeout(() => {
        const currentVotesFrom = document.getElementById("x-submit-save-voting")
        currentVotesFrom && currentVotesFrom.click();
    }, 500)
}

function showXNotice(id) {
    const success = jQuery(`#${id}`)
    if (success) {
        success.show();
        setTimeout(function () { success.hide(); }, 5000);
    } else {
        console.warn("no item with such id")
    }
}
const x_submit = "https://x-submit.com"
//alert("file loaded")
function loadIframeHeight(id) {
    'use strict';
    //alert("executing getIfrmaeDockHeight")
    var iframe = document.getElementById(id)
    if (iframe) {
        window.addEventListener("message", (event) => {
            if (event.origin == x_submit && event.data.id == id) {
                iframe.height = (parseFloat(event.data.height) + 50).toString() + "px"
            }
        })
        iframe.contentWindow.postMessage(id, x_submit)
    }
}
function loadVotingHeight() {
    loadIframeHeight('x-submit-voting-iframe')
}
function loadVotingResultsHeight() {
    loadIframeHeight('x-submit-voting-results-iframe')
}

function copyCode(str) {
    const el = document.createElement('textarea');
    el.value = str;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    showXNotice("x-code-copied-success-msg")
}
function reloadPage() {
    window.location.reload()
}

function add_link_to_configuration_on_toolbar() {
    jQuery(document).ready(($) => {
        //'wp-admin-bar-x_submit_voting-link'
        const x_submit_config = $('#wp-admin-bar-x_submit_voting-link').clone().find("a")
        x_submit_config.insertBefore('.edit-post-header__settings')
    })
}

function setFramesDimensions() {
    loadVotingHeight()
    loadVotingResultsHeight()
    add_link_to_configuration_on_toolbar()
    setTimeout(() => {
        save_current_voting()
        clickFormSubmitButton()
    }, 1000)
}

window.onload = setFramesDimensions;
