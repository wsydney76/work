// https://github.com/craftcms/cms/pull/8017
function compare(draftId, siteId, isProvisionalDraft) {

    if (draftId == -1)
        draftId = newDraftId

    url = `${compareUrl}&draftId=${draftId}&siteId=${siteId}&isProvisionalDraft=${isProvisionalDraft}`
    $.get(url, function(data) {
            var slideout = new Craft.Slideout(data, {
                containerAttributes: {class: 'compare-container'}
            });
        })
        .fail(function() {
            alert("Error");
        })
}

// TODO: Check window.draftEditor in Craft 4

if (window.draftEditor) {
    window.draftEditor.on('createProvisionalDraft', function() {
        newDraftId = window.draftEditor.settings.draftId
        $('#draft-new').css('display', '')
    });

}
