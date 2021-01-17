export const mapFile = file => ({
  started: false,
  finished: false,
  failed: false,
  data: file,
});

export const mapFileUploadStarted = (files, uploadIndex) => files.map((file, index) => {
  if (uploadIndex !== index) {
    return file;
  }

  return {
    ...file,
    started: true,
  }
});

export const mapFileUploadFinished = (files, uploadIndex) => files.map((file, index) => {
  if (uploadIndex !== index) {
    return file;
  }

  return {
    ...file,
    started: false,
    finished: true,
  }
});

export const mapFileUploadFailed = (files, uploadIndex) => files.map((file, index) => {
  if (uploadIndex !== index) {
    return file;
  }

  return {
    ...file,
    started: false,
    failed: true,
  }
});
