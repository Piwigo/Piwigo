<div class="titrePage">{L_TITLE}</div>
<br />
<form method="post" action="{F_ACTION}" style="text-align:center">
<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->
<table>
  <!-- BEGIN textfield -->
  <tr>
    <td></td>
    <td>{textfield.L_NAME} *</td>
    <td>
      <input type="text" name="{textfield.NAME}-content" value="{textfield.VALUE}" size="40" />
      <input class="radio" type="radio" name="{textfield.NAME}-mode" value="OR" {textfield.OR_CHECKED} /> {L_SEARCH_OR}
      <input class="radio" type="radio" name="{textfield.NAME}-mode" value="AND" {textfield.AND_CHECKED} /> {L_SEARCH_AND}
    </td>
  </tr>
  <!-- END textfield -->
  <tr>
   <td colspan="3" style="text-align:center;">* {L_SEARCH_COMMENTS}</td>
  </tr>
  <!-- BEGIN datefield -->
  <tr>
    <td></td>
    <td>{datefield.L_NAME}</td>
    <td>
      <table>
        <tr>
          <td style="text-align:left;"><input type="checkbox" name="{datefield.NAME}-check" value="1" {datefield.CHECKED} /> {L_SEARCH_DATE_IS}</td>
          <td style="text-align:left;">
            <select name="{datefield.NAME}:year">
              <!-- BEGIN year_option -->
              <option{datefield.year_option.SELECTED}>{datefield.year_option.OPTION}</option>
              <!-- END year_option -->
            </select>
            <select name="{datefield.NAME}:month">
              <!-- BEGIN month_option -->
              <option{datefield.month_option.SELECTED}>{datefield.month_option.OPTION}</option>
              <!-- END month_option -->
            </select>
            <select name="{datefield.NAME}:day">
              <!-- BEGIN day_option -->
              <option{datefield.day_option.SELECTED}>{datefield.day_option.OPTION}</option>
              <!-- END day_option -->
            </select>
          </td>
        </tr>
        <tr>
          <td style="text-align:left;"><input type="checkbox" name="{datefield.NAME}-after-check" value="1" {datefield.AFTER_CHECKED} /> {L_SEARCH_DATE_IS_AFTER}</td>
          <td style="text-align:left;">
            <select name="{datefield.NAME}-after:year">
              <!-- BEGIN after_year_option -->
              <option{datefield.after_year_option.SELECTED}>{datefield.after_year_option.OPTION}</option>
              <!-- END after_year_option -->
            </select>
            <select name="{datefield.NAME}-after:month">
              <!-- BEGIN after_month_option -->
              <option{datefield.after_month_option.SELECTED}>{datefield.after_month_option.OPTION}</option>
              <!-- END after_month_option -->
            </select>
            <select name="{datefield.NAME}-after:day">
              <!-- BEGIN after_day_option -->
              <option{datefield.after_day_option.SELECTED}>{datefield.after_day_option.OPTION}</option>
              <!-- END after_day_option -->
            </select>
            <input type="checkbox" name="{datefield.NAME}-after-included" value="1" {datefield.AFTER_INCLUDED_CHECKED} /> {L_SEARCH_DATE_INCLUDED}
          </td>
        </tr>
        <tr>
          <td style="text-align:left;"><input type="checkbox" name="{datefield.NAME}-before-check" value="1" {datefield.BEFORE_CHECKED} /> {L_SEARCH_DATE_IS_BEFORE}</td>
          <td style="text-align:left;">
            <select name="{datefield.NAME}-before:year">
              <!-- BEGIN before_year_option -->
              <option{datefield.before_year_option.SELECTED}>{datefield.before_year_option.OPTION}</option>
              <!-- END before_year_option -->
            </select>
            <select name="{datefield.NAME}-before:month">
              <!-- BEGIN before_month_option -->
              <option{datefield.before_month_option.SELECTED}>{datefield.before_month_option.OPTION}</option>
              <!-- END before_month_option -->
            </select>
            <select name="{datefield.NAME}-before:day">
              <!-- BEGIN before_day_option -->
              <option{datefield.before_day_option.SELECTED}>{datefield.before_day_option.OPTION}</option>
              <!-- END before_day_option -->
            </select>
            <input type="checkbox" name="{datefield.NAME}-before-included" value="1" {datefield.BEFORE_INCLUDED_CHECKED} /> {L_SEARCH_DATE_INCLUDED}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
  </tr>
  <!-- END datefield -->
  <tr>
    <td><input type="checkbox" name="categories-check" value="1" {CATEGORIES_SELECTED} /></td>
    <td>{L_SEARCH_CATEGORIES}</td>
    <td>
      <select style="width:500px" name="cat[]" multiple="multiple" size="10">
        <!-- BEGIN category_option -->
        <option {category_option.SELECTED} value="{category_option.VALUE}">{category_option.OPTION}</option>
        <!-- END category_option -->
      </select>
      <input type="checkbox" name="subcats-included" value="1" {CATEGORIES_SUBCATS_SELECTED} /> {L_SEARCH_SUBCATS_INCLUDED}
    </td>
  </tr>
</table>
<input class="radio" type="radio" name="mode" value="OR" {OR_CHECKED} /> {L_SEARCH_OR_CLAUSES}
<input class="radio" type="radio" name="mode" value="AND" {AND_CHECKED} /> {L_SEARCH_AND_CLAUSES}<br /><br />
<input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" /><br /><br />
<a href="{U_HOME}">[ {L_RETURN} ]</a>
