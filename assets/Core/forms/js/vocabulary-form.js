const VocabularyForm = {
    init : function() {
        $('#vocabulary_parent').on('change', VocabularyForm.updateId);
        $('#vocabulary_name').on('keyup', VocabularyForm.updateId);
        VocabularyForm.updateId();
    },

    updateId : function() {
        let parent = $('#vocabulary_parent').val();
        let $vocabularyName = $('#vocabulary_name');
        let name = $vocabularyName.val();
        name = name.replace('.', ' ');
        $vocabularyName.val(name);
        let id = parent!=='' && name!=='' ? parent+'.'+name : '';
        id = id
            .replace(/[^a-z0-9\.]/ig, ' ')
            .split(' ')
            .map(x => x.toLowerCase())
            .join('_')
        ;
        $('#vocabulary_id').val(id);
    }
};

$(() => {
    VocabularyForm.init();
});
