import React from "react";

import {URLS} from "../../../common/urls";

const BreadcrumbElement = ({ parent, index, numParents }) => {
  if ((index + 1) === numParents) {
    return <li>{parent.name || parent.courseCode}</li>;
  }

  return <li><a href={parent.url}>{parent.name || parent.courseCode}</a></li>;
};

export const Breadcrumbs = ({ data_finished, data_failed, parents }) => (
  <ol className="breadcrumb">
    <li><a href={URLS.frontpage}>Hjem</a></li>
    <li><a href={URLS.courses}>Emner</a></li>
    {data_finished && !data_failed && parents.map((parent, index) =>
      <BreadcrumbElement
        key={index}
        parent={parent}
        index={index}
        numParents={parents.length}
      />
    )}
  </ol>
);
