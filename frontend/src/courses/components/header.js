import React from 'react';

import { HeaderColumn } from './header-column';
import {COLUMN_CODE, COLUMN_NAME} from "../constants";
import {formatNumber} from "../../common/utils";

export const Header = ({ column, order, changeOrder, numCourses }) => (
  <div className="course-row course-row__header">
    <HeaderColumn
      id={COLUMN_CODE}
      text="Emnekode"
      column={column}
      order={order}
      changeOrder={changeOrder}
    />
    <HeaderColumn
      id={COLUMN_NAME}
      text={`Emnernavn ${numCourses === 0 ? '' : `(${formatNumber(numCourses)})`}`}
      column={column}
      order={order}
      changeOrder={changeOrder}
    />
  </div>
);
