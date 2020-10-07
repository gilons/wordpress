
jQuery(document).ready(function ($) {
    $('#nav a').last().addClass('last');
})
const x_submit = "https://localhost"
//alert("file loaded")
function loadIframeHeight(id) {
    'use strict';
    //alert("executing getIfrmaeDockHeight")
    var iframe = document.getElementById(id)
    if (iframe) {
        window.addEventListener("message", (event) => {
            if (event.origin == x_submit && event.data.id == id) {
                iframe.height = (parseFloat(event.data.height)+50).toString() + "px"
            }
        })
        iframe.contentWindow.postMessage(id, x_submit)
    }
}
function loadVotingHeight() {
    loadIframeHeight('x-submit-voting-iframe')
}
function loadVotingResultsHeight(){
    loadIframeHeight('x-submit-voting-results-iframe')
}

function copyCode(str) {
    const el = document.createElement('textarea');
    el.value = str;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
}
function reloadPage() {
    window.location.reload()
}

function add_link_to_configuration_on_toolbar(){
    jQuery(document).ready(($) => {
        //'wp-admin-bar-x_submit_voting-link'
        const x_submit_config = $('#wp-admin-bar-x_submit_voting-link').clone().find("a")
        x_submit_config.insertBefore('.edit-post-header__settings')
    })
}

function setFramesDimensions(){
    loadVotingHeight()
    loadVotingResultsHeight()
    add_link_to_configuration_on_toolbar()
}

window.onload = setFramesDimensions;
