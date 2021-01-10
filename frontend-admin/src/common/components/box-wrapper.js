import React from 'react';

import { StencilItemList } from "./stencilate/item-list";
import { EmptyItem } from "./empty-item";

const DEFAULT_STENCIL_SIZE = 10;

export const TITLE_SIZE_H2 = 'h2';
export const TITLE_SIZE_H3 = 'h3';

export const BoxWrapper = ({ title, titleSize = TITLE_SIZE_H2, titleInline, isLoading, isEmpty, dropdown, children, stencil_size = DEFAULT_STENCIL_SIZE }) => (
  <React.Fragment>
    <div className="list-header">
      {titleSize === TITLE_SIZE_H2 && <h2 className={titleInline ? 'inline-title' : ''}>{title}</h2>}
      {titleSize === TITLE_SIZE_H3 && <h3 className={titleInline ? 'inline-title' : ''}>{title}</h3>}

      {!isLoading && dropdown}
    </div>
    <ul className="list-group">
      {isLoading && <StencilItemList size={stencil_size} />}
      {isEmpty && <EmptyItem text='Det er ingenting her' />}
      {children}
    </ul>
  </React.Fragment>
);

BoxWrapper.defaultProps = {
  titleInline: false
};
