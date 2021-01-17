import nbLocale from "date-fns/locale/nb";
import React from "react";

import formatDistanceToNow from "date-fns/formatDistanceToNow";

import { formatJavaScriptDateHumanReadable } from "../../common/utils";

export const ItemTimeAgo = ({ datetime }) => (
  <span className="help" data-toggle="tooltip" title={formatJavaScriptDateHumanReadable(datetime)}>
    [{formatDistanceToNow(datetime, { locale: nbLocale, addSuffix: true })}]
  </span>
);
