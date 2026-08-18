let online = true;

export function setOnline(status) {
  online = !!status;
}

export function isOnline() {
  return online;
}

export function getOnlineStatus() {
  return online;
}
