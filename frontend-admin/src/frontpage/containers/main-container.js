import React from 'react';

import InfoBoxesContainer from './info-boxes-container';
import {MainBoxesContainer} from "./main-boxes-container";

export const MainContainer = () => (
  <React.Fragment>
    <InfoBoxesContainer />
    <MainBoxesContainer />
  </React.Fragment>

);
