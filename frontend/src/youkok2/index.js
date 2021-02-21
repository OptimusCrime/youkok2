import { run as archive } from "../archive";
import { run as courses } from "../courses";
import { run as frontpage } from "../frontpage";
import { run as searchBar } from "../searchBar";
import { run as sidebarPopular } from "../sidebarPopular";

import './site.less';

export const bootstrap = () => {
  if (document.getElementById('archive')) {
    archive();
  }
  if (document.getElementById('courses')) {
    courses();
  }
  if (document.getElementById('frontpage')) {
    frontpage();
  }
  if (document.getElementById('search-bar')) {
    searchBar('site');
  }
  if (document.getElementById('sidebar-popular')) {
    sidebarPopular();
  }

  // Other things that need bootstrapping
  const toggle = document.querySelector('.navbar-toggle');
  if (toggle) {
    document.querySelector('.navbar-toggle').addEventListener(
      'click',
      () => document.querySelector('.navbar-collapse').classList.toggle('in')
    );
  }
};
