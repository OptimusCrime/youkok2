import nbLocale from "date-fns/locale/nb";
import React from "react";

import distanceInWordsToNow from "date-fns/distance_in_words_to_now";

import { formatJavaScriptDateHumanReadable } from "../../common/utils";

export const ItemTimeAgo = ({ datetime }) => (
  <span className="tooltip" data-toggle="tooltip" title={formatJavaScriptDateHumanReadable(datetime)}>
    [{distanceInWordsToNow(datetime, { locale: nbLocale, addSuffix: true })}]
  </span>
);
