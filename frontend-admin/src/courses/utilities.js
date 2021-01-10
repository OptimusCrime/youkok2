import {RELATIVE_BUTTONS_LEFT, TOTAL_NUMBER_OF_RELATIVE_BUTTONS} from "./constants";

// There has to be a better way to do this...
export const calculateRelativeButtons = (page, numberOfPages, direction) => {
  // Subtract 1, as we always show the current page (as a disabled button)
  const relativeButtons = TOTAL_NUMBER_OF_RELATIVE_BUTTONS - 1;

  const maxLeftSide = Math.floor(relativeButtons / 2);
  const maxRightSide = Math.ceil(relativeButtons / 2);

  const tempNumLeft = calculateRelativeButtonsLeft(page, maxLeftSide);
  const tempNumRight = calculateRelativeButtonsRight(page, numberOfPages, maxRightSide);

  // If the total number of temp buttons is the same as the number of relativeButtons, then we do not need
  // to add to either sides
  if (tempNumLeft + tempNumRight === relativeButtons) {
    if (direction === RELATIVE_BUTTONS_LEFT) {
      return constructLeftArray(page, tempNumLeft);
    }

    return constructRightArray(page, tempNumRight);
  }

  const missingButtons = relativeButtons - tempNumRight - tempNumLeft;

  if (direction === RELATIVE_BUTTONS_LEFT) {
    if (tempNumLeft < maxLeftSide) {
      // Left was shorted, no need to calculate more
      return constructLeftArray(page, tempNumLeft);
    }

    return constructLeftArray(page, calculateRelativeButtonsLeft(page, maxLeftSide + missingButtons));
  }

  if (tempNumRight < maxRightSide) {
    // Right was shorted, no need to calculate more
    return constructRightArray(page, tempNumRight);
  }

  return constructRightArray(page, calculateRelativeButtonsRight(page, numberOfPages, maxRightSide + missingButtons));
};

const constructLeftArray = (page, length) => [...Array(length).keys()].map(v => page - 1 - v).reverse();

const constructRightArray = (page, length) => [...Array(length).keys()].map(v => page + 1 + v);

const calculateRelativeButtonsLeft = (page, maxLeft) => {
  if (page === 0) {
    return 0;
  }

  if (page - maxLeft < 0) {
    return page;
  }

  return maxLeft;
};

const calculateRelativeButtonsRight = (page, numberOfPages, maxRight) => {
  if (page + maxRight > numberOfPages) {
    return numberOfPages - page;
  }

  return maxRight;
};
