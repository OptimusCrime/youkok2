export const getAllKeys = () => {
  try {
    return Object.keys(localStorage);
  }
  catch (exception) {
    return [];
  }
};

export const getItem = key => {
  try {
    return localStorage.getItem(key);
  }
  catch (exception) {
    return null;
  }
};

export const keyExists = key => getItem(key) !== null;

export const setItem = (key, value) => {
  try {
    return localStorage.setItem(key, value);
  }
  catch (exception) {
    // Do nothing
  }
};

export const removeItem = key => {
  try {
    return localStorage.removeItem(key);
  }
  catch (exception) {
    // Do nothing
  }
};
