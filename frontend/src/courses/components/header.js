import React from 'react';

import { HeaderColumn } from './header-column';
import {COLUMN_CODE, COLUMN_NAME} from "../constants";

export const Header = ({ sortColumn, sortOrder, changeOrder }) => (
  <div className="course-row course-row__header">
    <HeaderColumn
      id={COLUMN_CODE}
      text="Emnekode"
      sortColumn={sortColumn}
      sortOrder={sortOrder}
      changeOrder={changeOrder}
    />
    <HeaderColumn
      id={COLUMN_NAME}
      text="Emnenavn"
      sortColumn={sortColumn}
      sortOrder={sortOrder}
      changeOrder={changeOrder}
    />
  </div>
);
