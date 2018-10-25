export const mapFrontpageInfo = data => ({
  number_files: data.number_files,
  number_downloads: data.number_downloads,
  number_courses_with_content: data.number_courses_with_content,
  number_new_elements: data.number_new_elements,
});

export const mapFrontpage = data => ({
  latest_elements: data.latest_elements,
  last_downloaded: data.last_downloaded,
  courses_last_visited: data.courses_last_visited,

  elements_most_popular: data.elements_most_popular,
  courses_most_popular: data.courses_most_popular,
});