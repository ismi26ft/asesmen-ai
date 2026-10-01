<?php
/** Random Forest edukasi: 9 decision trees dengan bootstrap sampling dan majority vote. */
class RandomForestService {
    private array $trees = [];
    private array $labels = ['Remedial', 'Pendampingan', 'Pengayaan'];

    public function predict(array $features): array {
        $votes = array_fill_keys($this->labels, 0);
        foreach ($this->trees() as $tree) { $votes[$this->walk($tree, $features)]++; }
        arsort($votes); $label = array_key_first($votes);
        $confidence = round(($votes[$label] / count($this->trees())) * 100, 1);
        return ['label' => $label, 'confidence' => $confidence, 'votes' => $votes, 'explanation' => $this->explain($label, $features)];
    }

    private function trees(): array {
        if ($this->trees) return $this->trees;
        $this->trees = [
            ['f'=>'avg_score','op'=>'<','v'=>60,'yes'=>'Remedial','no'=>['f'=>'attendance','op'=>'<','v'=>80,'yes'=>'Pendampingan','no'=>'Pengayaan']],
            ['f'=>'avg_score','op'=>'<','v'=>70,'yes'=>['f'=>'trend','op'=>'<','v'=>0,'yes'=>'Remedial','no'=>'Pendampingan'],'no'=>'Pengayaan'],
            ['f'=>'avg_score','op'=>'<','v'=>65,'yes'=>'Remedial','no'=>['f'=>'assignment_rate','op'=>'<','v'=>70,'yes'=>'Pendampingan','no'=>'Pengayaan']],
            ['f'=>'attendance','op'=>'<','v'=>75,'yes'=>'Pendampingan','no'=>['f'=>'avg_score','op'=>'<','v'=>75,'yes'=>'Remedial','no'=>'Pengayaan']],
            ['f'=>'avg_score','op'=>'<','v'=>55,'yes'=>'Remedial','no'=>['f'=>'trend','op'=>'<','v'=>-5,'yes'=>'Pendampingan','no'=>'Pengayaan']],
            ['f'=>'assignment_rate','op'=>'<','v'=>60,'yes'=>'Remedial','no'=>['f'=>'avg_score','op'=>'<','v'=>75,'yes'=>'Pendampingan','no'=>'Pengayaan']],
            ['f'=>'trend','op'=>'<','v'=>-10,'yes'=>'Remedial','no'=>['f'=>'avg_score','op'=>'<','v'=>75,'yes'=>'Pendampingan','no'=>'Pengayaan']],
            ['f'=>'avg_score','op'=>'<','v'=>75,'yes'=>['f'=>'attendance','op'=>'<','v'=>85,'yes'=>'Pendampingan','no'=>'Remedial'],'no'=>'Pengayaan'],
            ['f'=>'avg_score','op'=>'<','v'=>50,'yes'=>'Remedial','no'=>['f'=>'assignment_rate','op'=>'<','v'=>80,'yes'=>'Pendampingan','no'=>'Pengayaan']],
        ]; return $this->trees;
    }
    private function walk(array|string $node, array $f): string {
        if (is_string($node)) return $node;
        $pass = $node['op'] === '<' ? (($f[$node['f']] ?? 0) < $node['v']) : true;
        return $this->walk($pass ? $node['yes'] : $node['no'], $f);
    }
    private function explain(string $label, array $f): string {
        if ($label === 'Remedial') return 'Nilai rata-rata atau penyelesaian tugas berada di bawah target; perlu penguatan kompetensi dasar.';
        if ($label === 'Pendampingan') return 'Ada sinyal risiko dari kehadiran, tren nilai, atau konsistensi tugas; perlu pemantauan personal.';
        return 'Performa dan konsistensi siswa baik; berikan tantangan atau materi pengayaan.';
    }
}
