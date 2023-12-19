import { defineStore } from 'pinia';

export const useAnnouncementStore = defineStore('announcement', {
  state: () => ({
    announcement: ''
  }),
  actions: {
    newAnnouncement(word) {
      this.announcement = word;
    },
  },
});
