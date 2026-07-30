            <!-- ===== TODAY VIEW (TIMEGRID) ===== -->
            <div id="today-view" class="flex-1 flex flex-col min-h-0 h-full w-full mx-auto animate-[pageFadeIn_0.5s_ease_both]">
                <!-- Header for Today View -->
                <div class="mb-5 flex items-center justify-between shrink-0 px-2">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-teal-500/30">
                            <i data-lucide="layout-grid" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-slate-800 tracking-tight font-display">Tabel Jadwal: {{ $parsedDate->translatedFormat('d F Y') }}</h2>
                            <p class="text-sm font-bold text-teal-600 uppercase tracking-widest mt-0.5">Pantauan Seluruh Ruangan</p>
                        </div>
                    </div>
                </div>

                <!-- Scrollable TimeGrid Container -->
                <div class="flex-1 bg-white rounded-[2.5rem] shadow-[0_15px_40px_-10px_rgba(0,0,0,0.1)] border border-slate-200 overflow-auto relative custom-scrollbar flex">
                    <!-- Y-Axis (Time Labels) -->
                    <div class="w-20 shrink-0 sticky left-0 bg-white/95 backdrop-blur-md z-30 border-r border-slate-200 shadow-[2px_0_10px_rgba(0,0,0,0.02)]">
                        <div class="h-16 border-b border-slate-200 sticky top-0 bg-white/95 backdrop-blur-md z-40"></div> <!-- Top Left Corner -->
                        @for($i=7; $i<=17; $i++)
                            <div class="h-[70px] relative border-b border-slate-100 flex items-start justify-end pr-3 pt-2">
                                <span class="text-xs font-black text-slate-400">{{ sprintf('%02d:00', $i) }}</span>
                            </div>
                        @endfor
                    </div>
                    
                    <!-- X-Axis & Grid Body (Rooms) -->
                    <div class="flex min-w-max bg-slate-50/30">
                        @foreach($rooms as $room)
                            <!-- Kolom Ruangan -->
                            <div class="w-[280px] shrink-0 border-r border-slate-200 relative group">
                                <!-- Column Header (Room Name) -->
                                <div class="h-16 border-b border-slate-200 bg-white/95 backdrop-blur-md sticky top-0 z-20 flex flex-col items-center justify-center p-2 shadow-sm transition-colors group-hover:bg-slate-50">
                                    <span class="font-black text-slate-700 text-sm text-center leading-tight">{{ $room->name }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $room->capacity }} Kursi</span>
                                </div>
                                
                                <!-- Grid Lines & Events Container -->
                                <div class="relative h-[770px]"> <!-- 11 hours * 70px = 770px -->
                                    @for($i=7; $i<=17; $i++)
                                        <div class="h-[70px] border-b border-slate-100 w-full transition-colors group-hover:bg-slate-100/30"></div>
                                    @endfor
                                    
                                    <!-- Place Events for this room -->
                                    @foreach($reservations->where('room_id', $room->id) as $res)
                                        @php
                                            $startCarbon = \Carbon\Carbon::parse($res->start_time);
                                            $endCarbon = \Carbon\Carbon::parse($res->end_time);
                                            
                                            // Hitung jam & menit
                                            $startHour = (int) $startCarbon->format('H');
                                            $startMin = (int) $startCarbon->format('i');
                                            $endHour = (int) $endCarbon->format('H');
                                            $endMin = (int) $endCarbon->format('i');
                                            
                                            // Ratio: 70px per 60 mins = 1.1666px per min
                                            $pxPerMin = 70 / 60;
                                            
                                            // Hitung offset dari jam 07:00
                                            $top = (($startHour - 7) * 60 + $startMin) * $pxPerMin;
                                            $duration = $endCarbon->diffInMinutes($startCarbon);
                                            $height = $duration * $pxPerMin;
                                            
                                            // Batas operasional: 07:00 - 18:00 (Total 11 Jam = 770px)
                                            // Jika data berada SEPENUHNYA di luar batas, maka tidak dirender (continue)
                                            if ($top >= 770 || $top + $height <= 0) {
                                                continue;
                                            }

                                            // Jika jadwal melebihi jam operasional di bawah (lewat 18:00), potong tampilannya
                                            if ($top + $height > 770) {
                                                $height = 770 - $top;
                                            }

                                            // Jika jadwal dimulai sebelum 07:00, potong bagian atasnya
                                            if ($top < 0) {
                                                $height += $top;
                                                $top = 0;
                                            }
                                            
                                            // Tambahkan "Jarak/Gap" agar tidak mepet (margin atas dan bawah visual)
                                            $visualTop = $top + 2; 
                                            $visualHeight = max($height - 4, 15); // minimal tinggi 15px
                                            
                                            $modalData = [
                                                "room_name" => $res->room->name,
                                                "event_name" => $res->event_name,
                                                "requester_name" => $res->requester_name,
                                                "creator_name" => $res->creator->name ?? "Sistem",
                                                "time_range" => $startCarbon->format("H:i") . " - " . $endCarbon->format("H:i")
                                            ];
                                        @endphp
                                        
                                        <!-- Event Block -->
                                        <div class="absolute left-2 right-2 rounded-xl p-2.5 bg-gradient-to-br from-teal-50 to-white border border-teal-200 border-l-4 border-l-teal-500 shadow-sm cursor-pointer hover:shadow-lg hover:shadow-teal-500/20 hover:scale-[1.02] transition-all overflow-hidden z-10 group/event flex flex-col" 
                                             style="top: {{ $visualTop }}px; height: {{ $visualHeight }}px;"
                                             onclick='openReservationDetailModal(@json($modalData))'>
                                            <div class="text-[10px] font-black text-teal-600 tracking-wider shrink-0">{{ $startCarbon->format('H:i') }} - {{ $endCarbon->format('H:i') }}</div>
                                            <div class="text-[13px] font-bold text-slate-800 leading-snug mt-0.5 line-clamp-2 group-hover/event:text-teal-700 transition-colors flex-1">{{ $res->event_name }}</div>
                                            @if($visualHeight >= 60)
                                            <div class="absolute bottom-2 left-2.5 right-2.5 flex items-center gap-1 opacity-60 shrink-0">
                                                <i data-lucide="user" class="w-3 h-3 text-slate-500"></i>
                                                <span class="text-[10px] font-semibold text-slate-500 truncate">{{ $res->requester_name }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
