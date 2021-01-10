import React from 'react';

const VALUE_REGULAR = 0;
const VALUE_STRONG = 1;

const OPEN_STRONG = '<strong>';
const CLOSE_STRONG = '</strong>';

const updateHighlightArray = (searchWord, text, highlight) => {
  // This is the value we increase every find with
  const increaseInterval = searchWord.length;

  let offsetIndex = 0;
  let currentIndexOf = text.indexOf(searchWord, offsetIndex);

  while(currentIndexOf !== -1) {
    offsetIndex = currentIndexOf + increaseInterval;
    highlight.fill(VALUE_STRONG, currentIndexOf, offsetIndex);
    currentIndexOf = text.indexOf(searchWord, offsetIndex);
  }

  return highlight;
};

const constructHighlightArray = (codeHighlight, nameHighlight) => [
  ...codeHighlight,
  (codeHighlight.every(value => value === VALUE_STRONG) && nameHighlight[0] === 1) ? VALUE_STRONG : VALUE_REGULAR,
  ...nameHighlight
];

const insertHtmlLetter = letter => letter === '|' ? ' &mdash; ' : letter;

const renderHighlightHtml = (text, highlightArray) => {
  let output = '';
  let currentType = null;

  // Oh boy...
  highlightArray.forEach((value, index) => {
    const letter = text[index];

    if (currentType === null) {
      // Handle setting the initial type
      if (value === VALUE_STRONG) {
        output += `${OPEN_STRONG}${insertHtmlLetter(letter)}`;
        currentType = VALUE_STRONG;
      }
      else {
        output += insertHtmlLetter(letter);
        currentType = VALUE_REGULAR;
      }
    }
    else {
      // Handle previous type sat
      if ((currentType === VALUE_STRONG && value === VALUE_STRONG) || (currentType === VALUE_REGULAR && value === VALUE_REGULAR)) {
        // The type is the same, simply append
        output += insertHtmlLetter(letter);
      }
      else {
        // Switch type
        if (currentType === VALUE_REGULAR && value === VALUE_STRONG) {
          output += `${OPEN_STRONG}${insertHtmlLetter(letter)}`;
          currentType = VALUE_STRONG;
        }
        else {
          output += `${CLOSE_STRONG}${insertHtmlLetter(letter)}`;
          currentType = VALUE_REGULAR;
        }
      }
    }
  });

  // Remember to close dangling strong
  if (currentType === VALUE_STRONG) {
    output += CLOSE_STRONG;
  }

  return output;
};

export const highlightSearchResult = (searchString, course) => {
  const courseCode = course.code.toLowerCase();
  const courseName = course.name.toLowerCase();

  let courseCodeHighlight = new Array(courseCode.length).fill(VALUE_REGULAR);
  let courseNameHighlight = new Array(courseName.length).fill(VALUE_REGULAR);

  searchString.split(' ').filter(searchWord => /\w+/.test(searchWord)).forEach(searchWord => {
    if (courseCode.includes(searchWord)) {
      // .slice(0) clones the array
      courseCodeHighlight = updateHighlightArray(searchWord, courseCode, courseCodeHighlight.slice(0));
    }

    if (courseName.includes(searchWord)) {
      // .slice(0) clones the array
      courseNameHighlight = updateHighlightArray(searchWord, courseName, courseNameHighlight.slice(0));
    }
  });

  return renderHighlightHtml(
    `${course.code}|${course.name}`,
    constructHighlightArray(courseCodeHighlight, courseNameHighlight)
  );
};