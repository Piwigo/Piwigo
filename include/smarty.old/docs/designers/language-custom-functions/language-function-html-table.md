{html\_table} {#language.function.html.table}
=============

`{html_table}` is a [custom function](#language.custom.functions) that
dumps an array of data into an HTML `<table>`.

   Attribute Name    Type     Required      Default      Description
  ---------------- --------- ---------- ---------------- ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        loop         array      Yes          *n/a*       Array of data to loop through
        cols         mixed       No           *3*        Number of columns in the table or a comma-separated list of column heading names or an array of column heading names.if the cols-attribute is empty, but rows are given, then the number of cols is computed by the number of rows and the number of elements to display to be just enough cols to display all elements. If both, rows and cols, are omitted cols defaults to 3. if given as a list or array, the number of columns is computed from the number of elements in the list or array.
        rows        integer      No         *empty*      Number of rows in the table. if the rows-attribute is empty, but cols are given, then the number of rows is computed by the number of cols and the number of elements to display to be just enough rows to display all elements.
       inner        string       No          *cols*      Direction of consecutive elements in the loop-array to be rendered. *cols* means elements are displayed col-by-col. *rows* means elements are displayed row-by-row.
      caption       string       No         *empty*      Text to be used for the `<caption>` element of the table
    table\_attr     string       No      *border=\"1\"*  Attributes for `<table>` tag
      th\_attr      string       No         *empty*      Attributes for `<th>` tag (arrays are cycled)
      tr\_attr      string       No         *empty*      attributes for `<tr>` tag (arrays are cycled)
      td\_attr      string       No         *empty*      Attributes for `<td>` tag (arrays are cycled)
      trailpad      string       No         *&nbsp;*     Value to pad the trailing cells on last row with (if any)
        hdir        string       No         *right*      Direction of each row to be rendered. possible values: *right* (left-to-right), and *left* (right-to-left)
        vdir        string       No          *down*      Direction of each column to be rendered. possible values: *down* (top-to-bottom), *up* (bottom-to-top)

-   The `cols` attribute determines how many columns will be in the
    table.

-   The `table_attr`, `tr_attr` and `td_attr` values determine the
    attributes given to the `<table>`, `<tr>` and `<td>` tags.

-   If `tr_attr` or `td_attr` are arrays, they will be cycled through.

-   `trailpad` is the value put into the trailing cells on the last
    table row if there are any present.

<!-- -->


    <?php
    $smarty->assign( 'data', array(1,2,3,4,5,6,7,8,9) );
    $smarty->assign( 'tr', array('bgcolor="#eeeeee"','bgcolor="#dddddd"') );
    $smarty->display('index.tpl');
    ?>

      

The variables assigned from php could be displayed as these three
examples demonstrate. Each example shows the template followed by
output.


    {**** Example One ****}
    {html_table loop=$data}

    <table border="1">
    <tbody>
    <tr><td>1</td><td>2</td><td>3</td></tr>
    <tr><td>4</td><td>5</td><td>6</td></tr>
    <tr><td>7</td><td>8</td><td>9</td></tr>
    </tbody>
    </table>


    {**** Example Two ****}
    {html_table loop=$data cols=4 table_attr='border="0"'}

    <table border="0">
    <tbody>
    <tr><td>1</td><td>2</td><td>3</td><td>4</td></tr>
    <tr><td>5</td><td>6</td><td>7</td><td>8</td></tr>
    <tr><td>9</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    </tbody>
    </table>


    {**** Example Three ****}
    {html_table loop=$data cols="first,second,third,fourth" tr_attr=$tr}

    <table border="1">
    <thead>
    <tr>
    <th>first</th><th>second</th><th>third</th><th>fourth</th>
    </tr>
    </thead>
    <tbody>
    <tr bgcolor="#eeeeee"><td>1</td><td>2</td><td>3</td><td>4</td></tr>
    <tr bgcolor="#dddddd"><td>5</td><td>6</td><td>7</td><td>8</td></tr>
    <tr bgcolor="#eeeeee"><td>9</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    </tbody>
    </table>

      
