package com.composrfoundation.codequalitychecker;

class SearchFile implements Comparable<SearchFile> {

    public String path;
    public long date;

    public SearchFile(String path, long date) {
        this.path = path;
        this.date = date;
    }

    @Override
    public String toString() {
        return Long.toString(this.date);
    }

    @Override
    public int compareTo(SearchFile other) {
        if (((SearchFile) other).date == this.date) {
            return 0;
        }
        return ((SearchFile) other).date < this.date ? -1 : 1;
    }
}
