export const mapGraphData = graph => graph.map(data => {

  const {
    date,
    value
  } = data;

  const [year, month, day] = date.split('-').map(value => parseInt(value, 10));

  return {
    x: Date.UTC(year, month - 1, day),
    y: value
  }
});
