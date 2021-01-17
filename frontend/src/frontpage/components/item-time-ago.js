import React from "react";

import { format, formatDistanceToNow } from "../../common/utils";

export const ItemTimeAgo = ({ datetime }) => (
  <span className="help" data-toggle="tooltip" title={format(datetime)}>
    [{formatDistanceToNow(datetime)}]
  </span>
);
