import React from 'react';
import {connect} from 'react-redux';

import FilesListingContainer from "./files-listing-container";
import {Box} from "../components/box";

const PendingContainer = ({pending}) => {

  return (
    <Box
      content={
        <FilesListingContainer
          files={pending}
        />
      }
    >
      Elementer som venter på godkjenning
    </Box>
  );
};


export default connect(() => ({}), {})(PendingContainer);
