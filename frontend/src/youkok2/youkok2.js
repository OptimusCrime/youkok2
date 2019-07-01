import './youkok2.less';

const bootstrap = () => {
  // Toggle collapse top menu for devices
  document.querySelector('.navbar-toggle').addEventListener(
    'click',
    () => document.querySelector('.navbar-collapse').classList.toggle('in')
  );
};

document.addEventListener("DOMContentLoaded", bootstrap);
