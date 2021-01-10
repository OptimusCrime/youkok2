import React from "react";

import { formatNumber } from "../../../common/utils";
import { ELEMENT_TYPE_DIRECTORY } from "../../consts";
import { TYPE_LINK } from "../../../common/types";

export const ArchiveRow = ({ item }) => (
  <a
    className="archive-row"
    href={item.url}
    title={item.type === TYPE_LINK ? item.link : ''}
    target={item.type === TYPE_LINK ? '_blank' : '_self'}
  >
    <div className="archive-row__icon">
      <div style={{ backgroundImage: `url('assets/images/icons/${item.icon}')`}} />
    </div>
    <div className="archive-row__name">
      <span>{item.name}</span>
    </div>
    <div className="archive-row__downloads">
      <span>{item.type === ELEMENT_TYPE_DIRECTORY ? '' : formatNumber(item.downloads)}</span>
    </div>
    <div className="archive-row__age">
      <span>{item.added}</span>
    </div>
  </a>
);
