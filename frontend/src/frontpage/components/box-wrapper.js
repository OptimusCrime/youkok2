import React from 'react';

import { StencilItemList } from "./stencilate/item-list";
import {EmptyItem} from "./empty-item";

const STENCIL_SIZE = 10;

export const BoxWrapper = ({ title, titleInline, isLoading, isEmpty, dropdown, children }) => (
  <div className="col-xs-12 col-sm-6 frontpage-box">
    <div className="list-header">
      <h2 className={titleInline ? 'can-i-be-inline' : ''}>{title}</h2>
      {dropdown}
    </div>
    <ul className="list-group">
      {isLoading && <StencilItemList size={STENCIL_SIZE} />}
      {isEmpty && <EmptyItem text='Det er ingenting her' />}
      {children}
    </ul>
  </div>
);