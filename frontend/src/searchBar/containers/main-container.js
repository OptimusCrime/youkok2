import React from 'react';
import {connect} from 'react-redux';

import {
  updateSearchField as updateSearchFieldDispatch,
  updateCursorPosition as updateCursorPositionDispatch,
  closeSearchResults as closeSearchResultsDispatch,
} from '../redux/form/actions';
import {
  ARROW_DOWN,
  ARROW_UP,
  KEYCODE_ENTER,

  KEYCODE_ARROW_DOWN,
  KEYCODE_ARROW_UP,
} from "../constants";
import {highlightSearchResult} from "../utils/highlighter";
import {CLOSE_SEARCH_RESULTS} from "../redux/form/constants";

const MINIMUM_SEARCH_LENGTH = 2;

const MainContainer = props => {

  const {
    input_raw,
    input_display,
    results,
    cursor,
    updateCursorPosition,
    updateSearchField,
    closeSearchResults
  } = props;

  if (!window.COURSES_LOOKUP) {
    return null;
  }

  const courses = window.COURSES_LOOKUP;

  return (
    <React.Fragment>
      <input
        // TODO handle click outside the searchbar to remove the dropdown or something
        type="text"
        placeholder="Søk etter fag"
        className="form-control typeahead"
        onChange={e => {
          const keyCode = e.keyCode;

          if (keyCode !== KEYCODE_ARROW_UP && keyCode !== KEYCODE_ARROW_DOWN) {
            updateSearchField(e.target.value, courses)
          }
        }}
        onKeyDown={e => {
          const keyCode = e.keyCode;

          if (keyCode === KEYCODE_ENTER && cursor !== null) {
            e.preventDefault();

            // Make a direct lookup with the cursor.
            window.location.replace(`${window.location.origin}${results[cursor].url}`);
          }

          if (keyCode === KEYCODE_ARROW_UP || keyCode === KEYCODE_ARROW_DOWN) {
            updateCursorPosition(keyCode === KEYCODE_ARROW_DOWN ? ARROW_DOWN : ARROW_UP, cursor, results);
          }
        }}
        value={input_display}
        onBlur={closeSearchResults}
      />
      <button className="btn" type="button" id="nav-search">
        <i className="fa fa-search"/>
      </button>
      {results.length > 0 && input_display.length > MINIMUM_SEARCH_LENGTH &&
      <span className="tt-dropdown-menu" style={{
        position: 'absolute',
        top: '100%',
        left: '0px',
        zIndex: 100,
        right: 'auto',
        display: 'block'
      }}>
          <div className="tt-dataset-courses">
            <div className="tt-suggestions" style={{display: 'block'}}>
              {results.map((result, index) =>
                <div
                  key={result.id}
                  className={`tt-suggestion ${(cursor !== null && cursor === index) ? 'tt-cursor' : ''}`}
                >
                  <p
                    style={{whiteSpace: 'normal'}}
                    dangerouslySetInnerHTML={{
                      __html: highlightSearchResult(input_raw, result)
                    }}
                  />
                </div>
              )}
            </div>
          </div>
        </span>
      }
    </React.Fragment>
  );
};

const mapStateToProps = ({form}) => ({
  input_raw: form.input_raw,
  input_display: form.input_display,
  results: form.results,
  cursor: form.cursor,
});

const mapDispatchToProps = {
  updateSearchField: updateSearchFieldDispatch,
  updateCursorPosition: updateCursorPositionDispatch,
  closeSearchResults: ({ type: CLOSE_SEARCH_RESULTS }),
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);
