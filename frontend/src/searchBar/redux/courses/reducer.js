const defaultState = {
  courses: [
    { id: 1, name: 'Foo' },
    { id: 2, name: 'Fo2o' },
    { id: 3, name: 'Foo3' },
    { id: 4, name: 'Foo4' },
    { id: 5, name: 'Foo5' },
    { id: 6, name: 'Foo6' },
  ]
};

const courses = (state = defaultState, action) => {
  switch (action.type) {

    default:
      return state
  }
};

export default courses;