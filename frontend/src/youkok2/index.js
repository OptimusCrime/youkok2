import { run as archive } from "../archive";
import { run as courses } from "../courses";
import { run as frontpage } from "../frontpage";
import { run as searchBar } from "../searchBar";
import { run as sidebarHistory } from "../sidebarHistory";
import { run as sidebarPopular } from "../sidebarPopular";
import { run as sidebarPost } from "../sidebarPost";

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
    searchBar();
  }
  if (document.getElementById('sidebar-history')) {
    sidebarHistory();
  }
  if (document.getElementById('sidebar-popular')) {
    sidebarPopular();
  }
  if (document.getElementById('sidebar-post')) {
    sidebarPost();
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
