    function mainNavBarLoaded()
    {
        getPercentualeSviluppiGenerabili('main-nav-bar-sections-outer-container','300px','200px','dark1','10px 0px');
    }
    window.onload = function()
    {
        getTable("parametri");
    };
    function getTable(table,orderBy,orderType)
    {
        getEditableTable
        ({
            table:table,
            editable: true,
            container:'containerSommarioArchivi',
            readOnlyColumns:['id_parametro'],
            noInsertColumns:['id_parametro'],
            orderBy:orderBy,
            orderType:orderType
        });
    }
    function editableTableLoad()
    {

    }