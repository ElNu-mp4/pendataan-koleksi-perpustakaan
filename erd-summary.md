# ERD Summary for profile.perpus.jatengprov.go.id

## Key entity groups

### Survey / Pendataan
- `identitas_input_koleksi` (submission header)
- `jawaban_koleksi` (individual answers)
- `pertanyaan_koleksi` (survey questions)
- `opsi_pertanyaan_koleksi` (question options)
- `config_pendataan` (survey configuration)

### CMS content
- `berita` (news)
- `kategori` (news categories)
- `komentar` (news comments)
- `album` / `gallery` (photo albums)
- `playlist` / `video` / `komentarvid` (video content)
- `users`, `modul`, `users_modul` (user accounts and permissions)

## Main relationships

- `identitas_input_koleksi.id_koleksi` → `jawaban_koleksi.id_koleksi`
- `pertanyaan_koleksi.id_pertanyaan` → `jawaban_koleksi.id_pertanyaan`
- `pertanyaan_koleksi.id_pertanyaan` → `opsi_pertanyaan_koleksi.id_pertanyaan`
- `kategori.id_kategori` → `berita.id_kategori`
- `users.username` → `berita.username`
- `berita.id_berita` → `komentar.id_berita`
- `album.id_album` → `gallery.id_album`
- `playlist.id_playlist` → `video.id_playlist`
- `video.id_video` → `komentarvid.id_video`
- `users.id_session` → `users_modul.id_session`
- `modul.id_modul` → `users_modul.id_modul`

## Notes

- The only MySQL-enforced foreign key is:
  - `jawaban_koleksi.id_koleksi` → `identitas_input_koleksi.id_koleksi`
- Most of the rest of the model relies on column naming conventions and controller/view joins.
- The survey tables are using `InnoDB`, while much of the CMS content uses `MyISAM`.

## Files created
- `erd.puml`
- `erd-summary.md`
