<div class="panel-body">
    <div class="row new-line">
        <div class="col-sm-12">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <div class="col-sm-1" >
                        <label class="control-label">Periode</label>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control small " name="periode" onchange="Hk.getNoregMitraByRdim(this)">
                            <option value="">-- pilih periode --</option>
                            <?php foreach ($periodes as $periode): ?>
                                <option value="<?php echo $periode->id ?>"><?php echo tglIndonesia($periode->mulai, '-', ' ') . ' s.d ' . tglIndonesia($periode->selesai, '-', ' ') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1" >
                        <label class="control-label">Noreg</label>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control small" name="noreg">
                            <option value="">-- pilih periode --</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1" >
                        <label class="control-label">Mitra</label>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="nama-mitra" value="" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1" >
                        <label class="control-label">Populasi</label>
                    </div>
                    <div class="col-sm-1">
                        <input type="text" class="form-control text-right" name="populasi" value="" data-tipe="integer" readonly>
                    </div>
                </div>
                <hr>
                <div class="row new-line">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="col-sm-5" >
                                <label class="control-label">Tanggal timbang</label>
                            </div>
                            <div class="col-sm-5">
                                <!-- <div class="input-group">
                                    <input value="" type="text" class="form-control text-center date" placeholder="Start Date" name="timbangDate" data-tipe="date" readonly data-required="1">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div> -->
                                <div class="input-group date" id="tgl_timbang">
                                    <input type="text" class="form-control text-center" data-required="1" placeholder="Tanggal Timbang" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-5" >
                                <label class="control-label">Umur</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control text-right" name="umur" value="" data-tipe="integer" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-5" >
                                <label class="control-label">Jumlah Kematian</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control text-right" name="jml-kematian" value=""  data-tipe="integer">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-5" >
                                <label class="control-label">BB Rata2</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control text-right" name="bb-average" value=""  data-tipe="decimal">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="col-sm-4" >
                                <label class="control-label">Terima Pakan</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="terima-pakan" value="" data-tipe="integer">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4" >
                                <label class="control-label">Sisa Pakan di Kandang</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="sisa-pakan" value="" data-tipe="integer">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4" >
                                <label class="control-label">Komentar PIC</label>
                            </div>
                            <div class="col-sm-8">
                                <textarea name="komentar" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row new-line">
                    <div class="col-sm-4">
                        <table id="tb_sekat" class="table table-hover table-bordered custom_table table-form small">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">Jml sekat</th>
                                    <th class="col-sm-1">BB</th>
                                    <th class="col-sm-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input class="form-control text-right" type="text" name="sekat" value="" data-tipe="integer"></td>
                                    <td><input class="form-control text-right" type="text" name="bb" value="" data-tipe="decimal"></td>
                                    <td class="text-center action">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="Hk.removeRowTable(this)"> <i class="fa fa-minus"></i> </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="Hk.addRowTable(this)"> <i class="fa fa-plus"></i> </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary pull-right" onclick="Hk.save()"> <i class="fa fa-save"></i> | Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>