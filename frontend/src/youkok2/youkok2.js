import './youkok2.less';

const bootstrap = () => {
  document.querySelector('.navbar-toggle').addEventListener(
    'click',
    () => document.querySelector('.navbar-collapse').classList.toggle('in')
  );
};

document.addEventListener("DOMContentLoaded", bootstrap);
