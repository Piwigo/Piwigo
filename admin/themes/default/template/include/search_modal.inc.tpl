{footer_script}
$(document).ready(function () {

  function open_search_modal() {
    $("#HelpSearchModal").fadeIn();
}

});
{/footer_script}

<div id="HelpSearchModal" class="SearchModal">
    <div class="SearchModalContainer">
        <strong>{'Search with extended syntax'|@translate}</strong>
        <div>{'The quick search engine allows the use of Boolean operators to refine your search. By default, the search applies to all keywords. Searches are not case-sensitive. Here is a list of actions you can perform :'|@translate}</div>
        <legend>{'With keywords'|@translate}</legend>
        
        <div class="searchModalTab">
            <div class="searchModalContent">
                <p>{'Keywords in quotes'|@translate}</p>
                <code>"captain haddock"</code>
            </div>
            <div class="searchModalContent">{'Use quotes to search for an exact word or phrase'|@translate}</div>
        </div>

        <div class="searchModalTab">
            <div class="searchModalContent">
                <p>{'inclusive OR'|@translate}</p>
                <code>"alain **OR** bernard"</code>
            </div>
            <div class="searchModalContent">{'Use OR between keywords to get results containing one or the other'|@translate}</div>
        </div>

        <div class="searchModalTab">
            <div class="searchModalContent">
                <p>{'Exclusion'|@translate}</p>
                <code>- "NOT keyword"</code>
            </div>
            <div class="searchModalContent">{'Use a minus sign (-) or NOT before a keyword to exclude it from the search. Note that NOT acts as a filter. You cannot use only NOT operators and cannot combine OR and NOT'|@translate}</div>
        </div>

        <div class="searchModalTab">
            <div class="searchModalContent">
                <p>{'Grouping'|@translate}</p>
                <code>(parentheses)</code>
            </div>
            <div class="searchModalContent">{'Use parentheses to group keywords with AND/OR conditions'|@translate}</div>
        </div>

        <legend>{'By Filters'|@translate}</legend>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Tag'|@translate}</p>
                  <code> tag:"alain" </code>
              </div>
              <div class="searchModalContent">{'Searches only within tags, ignoring titles and descriptions'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'File'|@translate}</p>
                  <code> file:"DSC_" </code>
              </div>
              <div class="searchModalContent">{'Searches within file names'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Author'|@translate}</p>
                  <code> author:"Alain" </code>
              </div>
              <div class="searchModalContent">{'Searches by author'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Created Date'|@translate}</p>
                  <code> created:"2003" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos taken in 2003'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Width and Height'|@translate}</p>
                  <code> width:"" </code>
                  <code> height:"" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos of a specific width or height in pixels'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Size'|@translate}</p>
                  <code> size:">5m" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos larger than 5 million pixels'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Ratio'|@translate}</p>
                  <code> ratio:"3/4" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos with a specific aspect ratio'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Hits'|@translate}</p>
                  <code> hits:"" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos by the number of hits'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Score'|@translate}</p>
                  <code> score:"*" </code>
              </div>
              <div class="searchModalContent">{'Returns all photos with or without a score'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'Filesize'|@translate}</p>
                  <code> filesize:"1m..10m" </code>
              </div>
              <div class="searchModalContent">{'Searches for files within a size range'|@translate}</div>
          </div>

          <div class="searchModalTab">
              <div class="searchModalContent">
                  <p>{'ID'|@translate}</p>
                  <code> id:"123..126" </code>
              </div>
              <div class="searchModalContent">{'Searches for photos using their unique identifier'|@translate}</div>
          </div>

          <a class="icon-cancel CloseSearchModal"></a>
      </div>
  </div>