Variable scopes {#language.variable.scopes}
===============

You have the choice to assign variables to the scope of the main Smarty
object, data objects created with [`createData()`](#api.create.data),
and template objects created with
[`createTemplate()`](#api.create.template). These objects can be
chained. A template sees all the variables of its own object and all
variables assigned to the objects in its chain of parent objects.

By default templates which are rendered by
[`$smarty->display(...)`](#api.display) or
[`$smarty->fetch(...)`](#api.fetch) calls are automatically linked to
the Smarty object variable scope.

By assigning variables to individual data or template objects you have
full control which variables can be seen by a template.



    // assign variable to Smarty object scope
    $smarty->assign('foo','smarty');

    // assign variables to data object scope
    $data = $smarty->createData();
    $data->assign('foo','data');
    $data->assign('bar','bar-data');

    // assign variables to other data object scope
    $data2 = $smarty->createData($data);
    $data2->assign('bar','bar-data2');

    // assign variable to template object scope
    $tpl = $smarty->createTemplate('index.tpl');
    $tpl->assign('bar','bar-template');

    // assign variable to template object scope with link to Smarty object
    $tpl2 = $smarty->createTemplate('index.tpl',$smarty);
    $tpl2->assign('bar','bar-template2');

    // This display() does see $foo='smarty' from the $smarty object
    $smarty->display('index.tpl');

    // This display() does see $foo='data' and $bar='bar-data' from the data object $data
    $smarty->display('index.tpl',$data);

    // This display() does see $foo='data' from the data object $data 
    // and $bar='bar-data2' from the data object $data2
    $smarty->display('index.tpl',$data2);

    // This display() does see $bar='bar-template' from the template object $tpl
    $tpl->display();  // or $smarty->display($tpl);

    // This display() does see $bar='bar-template2' from the template object $tpl2
    // and $foo='smarty' form the Smarty object $foo
    $tpl2->display();  // or $smarty->display($tpl2);

        

See also [`assign()`](#api.assign), [`createData()`](#api.create.data)
and [`createTemplate()`](#api.create.template).
