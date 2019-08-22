import './youkok2.less';

const bootstrap = () => {
  const toggle = document.querySelector('.navbar-toggle');
  if (toggle) {
    document.querySelector('.navbar-toggle').addEventListener(
      'click',
      () => document.querySelector('.navbar-collapse').classList.toggle('in')
    );
  }
};

document.addEventListener("DOMContentLoaded", bootstrap);
