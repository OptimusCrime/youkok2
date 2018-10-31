import React from "react";

import { formatNumber } from "../../common/utils";
import { ELEMENT_TYPE_DIRECTORY } from "../consts";
import { TYPE_LINK } from "../../common/types";

export const ArchiveRow = ({ item }) => (
  <a className="archive-row" href={item.url} title={item.type === TYPE_LINK ? item.link : ''}>
    <div className="archive-row-icon">
      <div style={{ backgroundImage: `url('assets/images/icons/${item.icon}')`}} />
    </div>
    <div className="archive-row-name">
      <span>{item.name}</span>
    </div>
    <div className="archive-row-downloads">
      <span>{item.type === ELEMENT_TYPE_DIRECTORY ? '' : formatNumber(item.downloads)}</span>
    </div>
    <div className="archive-row-age">
      <span>{item.added}</span>
    </div>
  </a>
);