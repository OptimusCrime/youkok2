import React from 'react';
import {calculateRelativeButtons} from "../../utilities";
import {RELATIVE_BUTTONS_LEFT, RELATIVE_BUTTONS_RIGHT} from "../../constants";

export const Navigation = ({page, numberOfPages, changePage}) => (
  <div className="courses-navigation" role="toolbar">
    <div
      className={`courses-navigation__group courses-navigation__group--left ${page === 0 ? 'courses-navigation__group--hidden' : ''}`}
      role="group"
    >
      <NavigationButton page={0} changePage={changePage}>
        <i className="fa fa-angle-double-left" aria-hidden="true"/>
      </NavigationButton>
      <NavigationButton page={page - 1} changePage={changePage}>
        <i className="fa fa-angle-left" aria-hidden="true"/>
      </NavigationButton>
    </div>
    <div className="courses-navigation__group courses-navigation__group--center" role="group">
      {numberOfPages > 0 &&
      <NavigationRelativeButtons
        page={page}
        numberOfPages={numberOfPages}
        changePage={changePage}
      />
      }
    </div>
    <div
      className={`courses-navigation__group courses-navigation__group--right ${(page === numberOfPages || page === 0) ? 'courses-navigation__group--hidden' : ''}`}
      role="group"
    >
      <NavigationButton page={page + 1} changePage={changePage}>
        <i className="fa fa-angle-right" aria-hidden="true"/>
      </NavigationButton>
      <NavigationButton page={numberOfPages} changePage={changePage}>
        <i className="fa fa-angle-double-right" aria-hidden="true"/>
      </NavigationButton>
    </div>
  </div>
);

const NavigationRelativeButtons = ({ page, numberOfPages, changePage }) => {
  const buttons = [];

  // Left side
  buttons.push(calculateRelativeButtons(page, numberOfPages, RELATIVE_BUTTONS_LEFT)
    .map(page =>
      <NavigationButton
        page={page}
        changePage={changePage}
        key={page}
      >
        {page + 1}
      </NavigationButton>
    ));

  // Current page
  buttons.push(
    <NavigationButton
      page={page}
      changePage={changePage}
      disabled={true}
      key={page}
    >
      {page + 1}
    </NavigationButton>
  );

  // Right side
  buttons.push(calculateRelativeButtons(page, numberOfPages, RELATIVE_BUTTONS_RIGHT)
    .map(page =>
      <NavigationButton
        page={page}
        changePage={changePage}
        key={page}
      >
        {page + 1}
      </NavigationButton>
    ));

  return buttons;
};

const NavigationButton = ({children, page, changePage, disabled = false}) => (
  <button
    type="button"
    className="btn"
    disabled={disabled}
    onClick={() => {
    if (!disabled) {
      changePage(page);
    }
  }}
  >
    {children}
  </button>
);
