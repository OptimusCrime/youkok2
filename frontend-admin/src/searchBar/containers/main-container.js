import React from 'react';
import {connect} from 'react-redux';

import {
  updateSearchField as updateSearchFieldDispatch,
  updateCursorPosition as updateCursorPositionDispatch,
} from '../redux/form/actions';
import {
  ARROW_DOWN,
  ARROW_UP,
  KEYCODE_ARROW_DOWN,
  KEYCODE_ARROW_UP, MODE_SITE,
} from "../constants";
import {highlightSearchResult} from "../utils/highlighter";
import {CLOSE_SEARCH_RESULTS} from "../redux/form/constants";
import {KEYCODE_ENTER, SEARCH_QUERY_IDENTIFIER} from "../../common/constants";
import {URLS} from "../../common/urls";
import {getCourses, verifyLookup} from "../../common/coursesLookup";

const MINIMUM_SEARCH_LENGTH = 2;

const MainContainer = props => {
  const {
    input_raw,
    input_display,
    results,
    cursor,
    updateCursorPosition,
    updateSearchField,
    closeSearchResults,
    mode,
    admin_courses,
  } = props;

  const displayResults = results.length > 0 && input_display.length >= MINIMUM_SEARCH_LENGTH;

  return (
    <React.Fragment>
      <input
        type="text"
        placeholder="Søk etter fag"
        className="form-control search-bar"
        onChange={e => {
          const keyCode = e.keyCode;

          if (keyCode !== KEYCODE_ARROW_UP && keyCode !== KEYCODE_ARROW_DOWN) {
            const courses = mode === MODE_SITE ? getCourses() : admin_courses;

            updateSearchField(e.target.value, courses);
          }
        }}
        onFocus={
          () => mode === MODE_SITE
            ? verifyLookup()
            : () => {}
        }
        onKeyDown={e => {
          const keyCode = e.keyCode;

          if (keyCode === KEYCODE_ENTER) {
            e.preventDefault();

            if (cursor !== null) {
              // Make a direct lookup with the cursor.
              window.location.replace(`${window.location.origin}${results[cursor].url}`);
            }
            else {
              // Only allow search functionality for the actual site
              if (mode === MODE_SITE) {
                window.location.replace(`${URLS.courses}?${SEARCH_QUERY_IDENTIFIER}=${encodeURI(input_raw)}`);
              }
            }
          }

          if ((keyCode === KEYCODE_ARROW_UP || keyCode === KEYCODE_ARROW_DOWN) && displayResults) {
            updateCursorPosition(keyCode === KEYCODE_ARROW_DOWN ? ARROW_DOWN : ARROW_UP, cursor, results);
          }
        }}
        value={input_display}
        onBlur={closeSearchResults}
      />
      <button className="btn" type="button" id="nav-search">
        <i className="fa fa-search"/>
      </button>
      {displayResults && (
        <div className="search-bar__dropdown">
          {results.map((result, index) => (
            <a
              key={result.id}
              className={`search-bar__suggestion ${(cursor !== null && cursor === index) ? 'search-bar__cursor' : ''}`}
              href={result.url}
            >
              <p
                dangerouslySetInnerHTML={{
                  __html: highlightSearchResult(input_raw.toLowerCase(), result)
                }}
              />
              </a>
            )
          )}
        </div>
      )}
    </React.Fragment>
  );
};

const mapStateToProps = ({form, config}) => ({
  input_raw: form.input_raw,
  input_display: form.input_display,
  results: form.results,
  cursor: form.cursor,
  mode: config.mode,
  admin_courses: config.admin_courses,
});

const mapDispatchToProps = {
  updateSearchField: updateSearchFieldDispatch,
  updateCursorPosition: updateCursorPositionDispatch,
  closeSearchResults: ({ type: CLOSE_SEARCH_RESULTS }),
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);
