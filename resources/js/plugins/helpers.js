import {format} from "date-fns/format";

export function formatDate(value) {
  return format(value, 'dd.MM.yyyy HH:mm');
}
