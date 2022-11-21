export function initSearchSelect(
    $element,
    route,
    urlGenerator = function (route) {
        return params => Routing.generate(route, {q: params.term})
    }
) {
    $element.select2({
        theme       : 'bootstrap4',
        // placeholder is needed to allow clear
        placeholder : $element.find('option[value=""]').text(),
        // allow to clear value
        allowClear  : true,
        ajax        : {
            delay           : 250,
            url             : urlGenerator(route),
            dataType        : 'json',
            data:function (term, page) {
                return { term:term, page:page };
            },
            processResults  : data => {
                return {
                    results : data.map(({id, code}) => {
                        return {id, text: code}
                    })
                }
            }
        }
    })
}